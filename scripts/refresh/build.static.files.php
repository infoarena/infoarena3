#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__))."/__init_script__.php";
InfoarenaEnvironment::prepareForScript('build.static.files');

function add_a_hash($file) {
    $key = md5_file($file);

    $new_file = strrev($file);
    $parts = explode('/', $new_file, 2);
    $parts[0] .= '-'.substr($key, 0, 8);
    $new_name = strrev($parts[0]);
    $new_file = strrev(implode('/', $parts));
    /**
     * we just prepended the file with 8 characters built from the contents
     * this way if we rebuild the file without changing anything we have the
     * same filename
     */
    Filesystem::rename($file, $new_file);
    return $new_name;
}

/**
 * We will rebuild the global static files (javascript and css)
 * from the coffescript and sass files
 */

echo phutil_console_format("**>>>** Building static files\n\n");
$dictionary = array();

// We clean the result directory
// The Old Fashioned Way
Filesystem::remove(InfoarenaEnvironment::getRoot()."/web/assets/result");
Filesystem::createDirectory(
    InfoarenaEnvironment::getRoot()."/web/assets/result",
    0755);

$finder = new FileFinder(InfoarenaEnvironment::getRoot()."/web/assets/coffee");
$files = $finder->withType('f')->withSuffix('coffee')->find();

foreach ($files as &$file) {
    $file = InfoarenaEnvironment::getRoot()."/web/assets/coffee/".$file;
}

$tmp = Filesystem::createTemporaryDirectory("tmp_");
execx(
    "coffee --compile --join %s %Ls",
    $tmp . "/main.js",
    $files);

$script_location = null;
// We don't minify if we're in development mode
if (InfoarenaEnvironment::isDevelopmentModeOn()) {
    $script_location =
        InfoarenaEnvironment::getRoot() . "/web/assets/result/script.js";
    Filesystem::rename(
        $tmp . "/main.js",
        $script_location);
} else {
    $script_location =
        InfoarenaEnvironment::getRoot() . "/web/assets/result/script.min.js";
    execx(
        "uglifyjs %s -o %s",
        $tmp . "/main.js",
        $script_location);
}
Filesystem::remove($tmp);

$dictionary['js'] = add_a_hash($script_location);
echo phutil_console_format(
    "**>>>** <bg:green> **script.js** from coffeescript files built </bg>\n");



$css_output_style = "compressed";
$style_location =
    InfoarenaEnvironment::getRoot() . "/web/assets/result/style.min.css";
if (InfoarenaEnvironment::isDevelopmentModeOn()) {
    $css_output_style = "expanded";
    $style_location =
        InfoarenaEnvironment::getRoot() . "/web/assets/result/style.css";
}

execx(
    "sass %s %s --style %s",
    InfoarenaEnvironment::getRoot()."/web/assets/sass/main.sass",
    $style_location,
    $css_output_style);

$dictionary['css'] = add_a_hash($style_location);
echo phutil_console_format(
    "**>>>** <bg:green> **style.css** from sass files built </bg>\n\n");

foreach ($dictionary as $file) {
    FileSystem::changePermissions(
        InfoarenaEnvironment::getRoot() . "/web/assets/result/" . $file,
        0644);
}

echo phutil_console_format(
    "**>>>** <bg:green> Permissions for files set </bg>\n");

$helper = new PhutilJSON;

$generated_comment = <<<TEXT
/**
 * This file is generated, use ./scripts/bin/ia rebuild to regenerate it
 * @
TEXT;

$generated_comment .=
<<<TEXT
generated
 */

TEXT;

// writeFileIfChanged doesn't brake anything if some other process is reading
// solves any concurency issues
Filesystem::writeFileIfChanged(
    InfoarenaEnvironment::getRoot()."/conf/.static-files",
    $generated_comment . $helper->encodeFormatted($dictionary));
