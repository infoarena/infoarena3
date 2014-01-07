<?php

final class SessionModel extends BasicModel {
    /**
     * Gets the session information from the given key
     *
     * If no such session exists returns null
     *
     * @param string $key
     * @return SessionInfo
     */
    public function getSessionByKey($key) {
        $result = queryfx_one(
            $this->connection,
            "%Q %Q",
            $this->buildSelectClause(),
            $this->buildWhereClause($key));
        return new SessionInfo($result);
    }

   /**
     * @return string
     */
    private function buildSelectClause() {
        $session_table_name = $this->getTableName('session');
        return <<<SQL
SELECT
    {$session_table_name}.`skey` as `skey`,
    {$session_table_name}.`user_id` as `user_id`,
FROM {$session_table_name}
SQL;
    }

    /**
     * @param string $key
     * @return string
     */
    private function buildWhereClause($key) {
        $session_table_name = $this->getTableName('session', $raw = true);
        // FIXME: add a more generic form of hashing objects to be keys in mysql
        return qsprintf(
            $this->connection,
            "WHERE %T.`skey` = UNIHEX(%s)",
            $session_table_name,
            $key);
    }
}
