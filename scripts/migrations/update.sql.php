#!/usr/bin/env php
<?php
require_once dirname(dirname(__FILE__))."/__init_script__.php";
InfoarenaEnvironment::prepareForScript('update.sql');
$connection = InfoarenaEnvironment::getDatabaseConnection();

echo phutil_console_format("**>>>** Starting SQL Update\n\n");
$sql_patches = new SqlPatchModel($connection);
foreach ($sql_patches->getPatches() as $name => $patch) {
    try {
        if (!isset($patch['type'])) {
            throw new Exception("No type specified");
        }

        if (!isset($patch['file'])) {
            throw new Exception("No type specified");
        }

        if (!$sql_patches->isAccectableType($patch['type'])) {
            throw new Exception("Type " . $patch['type'] . " is not known");
        }

        $patch['path'] = InfoarenaEnvironment::getRoot() .
                         "/scripts/migrations/" . $patch['file'];
        if (!Filesystem::readFile($patch['path'])) {
            throw new Exception("File does not exists, or is not readable " .
                                "from this script");
        }
    } catch (Exception $e) {
        echo phutil_console_format(
            "**>>>>>** <bg:red> Error </bg> with patch `**%s**`. Aborting\n",
            $name);
        echo phutil_console_format(
            "      Reason: %s\n", $e->getMEssage());
        if (isset($patch['description'])) {
            echo phutil_console_format(
                "      Extra Information - Description: %s\n",
                $patch['description']);
        }
        break;
    }

    if (!$sql_patches->isInstalled($name)) {
        echo phutil_console_format(
            "**>>>>>** <bg:blue> Installing patch </bg> : `**%s**`\n",
            $name);
        $stdout = null;
        $stderr = null;
        try {
            $patch['name'] = $name;
            list($stdout, $stderr) = $sql_patches->apply($patch);
            if ($stdout) {
                echo phutil_console_format("Stdout: %s\n", $stdout);
            }

            if ($stderr) {
                echo phutil_console_format("Stderr: %s\n", $stderr);
            }

        } catch(Exception $e) {
            echo phutil_console_format(
                "**>>>>>** <bg:red> Error </bg> while patching `**%s**`\n",
                $name);
            echo phutil_console_format(
                "      %s", $e->getMessage());
            break;
        }
    }

    echo phutil_console_format(
        "**>>>>>** <bg:green> Patch Installed  </bg> : `**%s**`\n\n",
        $name);
}
