<?php

final class SqlPatchModel extends BasicModel {
    /**
     * Returns the current list of patches
     *
     * @return array
     */
    public function getPatches() {
        return array(
            'sql.patches' => array(
                'file' => '0.sql.patches.php',
                'type' => 'php',
                'description' =>
                    'The first sql patch, just adding support ' .
                    'for sql patches'));
    }

    /**
     * Returns the current list of accectable sql patches
     *
     * @return array
     */
    public function getAccectableTypes() {
        return array('php');
    }

    /**
     * Returns wether or not the given type is an accectable one
     *
     * @param string $type
     *
     * @return boolean
     */
    public function isAccectableType($type) {
        return in_array($type, $this->getAccectableTypes(), true);
    }

    /**
     * Checks if the current patch is installed or not
     *
     * @param AphrontDatabaseConnection $connection
     * @param string $patch_name
     *
     * @return boolean
     */
    public function isInstalled($patch_name) {
        if (!$this->tableExists('sql_patches')) {
            return false;
        }

        $patch_table = $this->getTableName('sql_patches', $raw = true);

        $result = queryfx_one(
            $this->connection,
            "SELECT COUNT(*) FROM %T WHERE %T.`name` = %s",
            $patch_table, $patch_table, $patch_name);

        return head($result) == 1;
    }

    /**
     * Applies this path
     *
     * @param array $patch
     * @return array($stdout, $stderr)
     */
    public function apply($patch) {
        $stdout = null;
        $stderr = null;
        list($stdout, $stderr) = execx("php %s", $patch['path']);

        $patch_table = $this->getTableName('sql_patches', $raw = true);

        queryfx(
            $this->connection,
            "REPLACE INTO %T VALUES(%s)",
            $patch_table, $patch['name']);
        return array($stdout, $stderr);
    }
}
