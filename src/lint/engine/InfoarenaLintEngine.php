<?php

/**
 * Infoarena Lint Engine
 *
 */
final class InfoarenaLintEngine extends PhutilLintEngine {
    public function buildLinters() {
        $linters = array();

        $paths = $this->getPaths();

        // Remove unnecessary paths
        foreach ($paths as $key => $path) {
            if (!$this->pathExists($path)) {
                unset($paths[$key]);
                continue;
            }

            if ($this->isExternalLibrary($path)) {
                unset($paths[$key]);
            }
        }

        $linters[] = id(new ArcanistPhutilLibraryLinter())->setPaths($paths);
        $linters[] = id(new ArcanistFilenameLinter())->setPaths($paths);

        // Keep only files that have sense
        foreach ($paths as $key => $path) {
            if (!is_file($this->getFilePathOnDisk($path))) {
                unset($paths[$key]);
                continue;
            }

            if (!$this->isProjectFile($path)) {
                unset($paths[$key]);
            }
        }

        $linters[] = id(new ArcanistGeneratedLinter())->setPaths($paths);
        $linters[] = id(new ArcanistNoLintLinter())->setPaths($paths);
        $linters[] = id(new ArcanistTextLinter())->setPaths($paths);
        $linters[] = id(new ArcanistSpellingLinter())->setPaths($paths);

        $php_paths = preg_grep("@\.php$@", $paths);
        $linters[] = id(new ArcanistXHPASTLinter())->setPaths($php_paths);

        $js_paths = preg_grep("@\.js$@", $paths);
        $linters[] = id(new ArcanistJSHintLinter())->setPaths($js_paths);

        $css_paths = preg_grep("@\.css$@", $paths);
        $linters[] = id(new ArcanistCSSLintLinter())->setPaths($css_paths);

        return $linters;
    }

    private function isExternalLibrary($path) {
        return preg_match("@^(arcanist|libphutil)/@", $path);
    }

    private function isProjectFile($path) {
        return preg_match("@\.(php|css|js)$@", $path);
    }
}
