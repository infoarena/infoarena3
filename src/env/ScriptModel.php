<?php

final class ScriptModel extends BasicModel {
    /**
     * @param string $table_name
     * @param array $columns
     * @param string $primary_key
     * @return $this
     */
    public function createTable($table_name,
                                array $columns,
                                $primary_key = null) {
        if (!is_string($table_name)) {
            throw new ParameterException(
                "ScriptModel::createTable expects `table_name` to be a string");
        }

        if (!is_array($columns)) {
            throw new ParameterException(
                "ScriptModel::createTable expects `columns` to be an array");
        }

        foreach ($columns as $column) {
            if (!is_string($column)) {
                throw new ParameterException(
                    "ScriptModel::createTable expects `columns` to contain " .
                    "only strings");
            }
        }

        if ($primary_key !== null && !is_string($primary_key)) {
            throw new ParameterException(
                "ScriptModel::createTable expects `primary_key to be null " .
                "or a string");
        }

        $patch_table_name = $this->getTableName($table_name);
        $query = <<<SQL
CREATE TABLE {$patch_table_name} (
SQL;

        $first_column = true;
        foreach ($columns as $column) {
            if (!$first_column) {
                $query .= ",";
            }

            $query .= $column;
        }

        if ($primary_key !== null) {
            $query .= ", PRIMARY KEY " . $primary_key;
        }

        $query .= ")";

        queryfx($this->connection, $query);
        return $this;
    }
}
