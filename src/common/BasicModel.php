<?php

/**
 * BasicModel from which all Model Types should inherit
 */
abstract class BasicModel {
    protected $connection;

    /**
     * Constructor for the basic model
     *
     * @param AphrontDatabaseConnection $connection
     * @return void
     */
    public function __construct(AphrontDatabaseConnection $connection) {
        $this->connection = $connection;
    }

    /**
     * Returns whether or not the given table exits
     *
     * @param string $table_name
     * @return bool
     */
    public function tableExists($table_name) {
        if (!is_string($table_name)) {
            throw new ParameterException(
                "BasicModel::tableExists expects `table_name` to be a string");
        }

        $result = queryfx_one(
            $this->connection,
            "SHOW TABLES LIKE %s",
            $this->getTableName($table_name, $raw = true));

        if ($result === null) {
            return false;
        }

        return head($result) ===
            $this->getTableName($table_name, $raw = true);
    }

    /**
     * Returns the real table name with the database prefix added
     *
     * If raw is true it returns the string before backticks are added
     *
     * @param string $old_table_name
     * @param bool $raw optional, by default false
     * @return string
     */
    public function getTableName($old_table_name, $raw = false) {
        if (!is_string($old_table_name)) {
            throw new ParameterException(
                "BasicModel::getTableName expects `old_table_name` to be a " .
                "string");
        }

        if (!is_bool($raw)) {
            throw new ParameterException(
                "BasicModel::getTableName expects `raw` to be a boolean");
        }

        $raw_table_name =
            InfoarenaEnvironment::getEnvConfig("db.prefix") . $old_table_name;

        if ($raw) {
            return $raw_table_name;
        }

        return qsprintf(
            $this->connection,
            "%T",
            $raw_table_name);
    }

}
