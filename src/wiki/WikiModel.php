<?php

/**
 *
 */
final class WikiModel extends BasicModel {
    /**
     * Gets the wiki with the given name path
     *  subsequently calls getWikiIdByNameAndParent
     *
     * @param string $name_path
     * @param int $language
     * @return Wiki|null
     */
    public function getWikiByNamePath($name_path, $language) {
        if (!is_string($name_path)) {
            throw new WikiParameterException(
                "WikiModel::getWikiByNamePath expects `name_path` to be a " .
                "string");
        }

        if (!Language::exists($language)) {
            throw new WikiParameterException(
                "WikiModel::getWikiByNamePath expects a language that exists");
        }

        $path = explode('/', $name_path);
        $id = 0;
        foreach ($path as $name) {
            $id = $this->getWikiIdByNameAndParent($name, $id);
        }

        if ($id === null)
            return null;
        return $this->getWikiById($id, $language);
    }

    /**
     * Gets the wiki id with the given name and parent id (and language)
     * To be called progressively for wiki's like
     * ancestor1/ancestor2/..../parent/wiki_name
     *
     * We don't need the language for this query because all wiki's with the
     * same language have the same id and parent_id
     *
     * @param string $name
     * @param int $parent_id
     * @return int|null
     */
    public function getWikiIdByNameAndParent($name, $parent_id) {
        if (!is_string($name)) {
            throw new WikiParameterException(
                "WikiModel::getWikiIdByNameAndParent expects `name` to be a " .
                "string");
        }

        if (!Wiki::isName($name)) {
            throw new WikiParameterException(
                "Invalid `name` given to WikiModel::getWikiIdByNameAndParent");
        }

        if (!is_int($parent_id)) {
            throw new WikiParameterException(
                "WikiModel::getWikiIdByNameAndParent expects `parent_id` to " .
                "be numeric");
        }

        $result = queryfx_one(
            $this->connection,
            "%Q %Q",
            $this->buildSelectIdClause(),
            $this->buildWhereByNameAndParentClause($name, $parent_id));

        if ($result === null)
            return null;
        return $result['id'];
   }

    /**
     * @param int $id
     * @return Wiki|null
     */
    public function getWikiById($id, $language) {
        if (!is_int($id)) {
            throw new WikiParameterException(
                "WikiModel::getWikiById expects `id` to be numeric");
        }

        if (!Language::exists($language)) {
            throw new WikiParameterException(
                "WikiModel::getWikiById expects a language that exists");
        }

        $result = queryfx_one(
            $this->connection,
            "%Q %Q %Q",
            $this->buildSelectClause(),
            $this->buildJoinClause(),
            $this->buildWhereByIdClause($id, $language));

        if ($result === null) {
            return null;
        }

        return new Wiki($result);
    }

    /**
     * @param int $revision_id
     * @return Wiki|null
     */
    public function getWikiByRevisionId($revision_id) {
        if (!is_int($revision_id)) {
            throw new WikiParameterException(
                "WikiModel::getWikiByRevisionId expects `revision_id` to be " .
                "numeric");
        }

        $result = queryfx_one(
            $this->connection,
            "%Q %Q",
            $this->buildSelectClause(),
            $this->buildJoinByRevisionIdClause());

        if ($result === null) {
            return null;
        }

        return new Wiki($result);
    }

    /**
     * @return string
     */
    private function buildSelectClause() {
        $wiki_table_name = $this->getTableName('wiki');
        $wiki_revision_table_name = $this->getTableName('wiki_revision');
        return <<<SQL
SELECT
    {$wiki_table_name}.`id` as `id`,
    {$wiki_table_name}.`name` as `name`,
    {$wiki_table_name}.`language` as `language`,
    {$wiki_table_name}.`creation_timestamp` as `creation_timestamp`,
    {$wiki_table_name}.`owner` as `owner`,
    {$wiki_table_name}.`security` as `security`,
    {$wiki_revision_table_name}.`id` as `revision_id`,
    {$wiki_revision_table_name}.`text` as `text`,
    {$wiki_revision_table_name}.`description` as `description`,
    {$wiki_revision_table_name}.`timestamp` as `timestamp`,
    {$wiki_revision_table_name}.`editor` as `editor`,
FROM {$wiki_table_name}
SQL;
    }

    /**
     * @return string
     */
    private function buildSelectIdClause() {
        $wiki_table_name = $this->getTableName('wiki');
        return <<<SQL
SELECT
    {$wiki_table_name}.`id` as `id`
FROM {$wiki_table_name}
SQL;
    }

    /**
     * @return string
     */
    private function buildJoinClause() {
        $wiki_table_name = $this->getTableName('wiki');
        $wiki_revision_table_name = $this->getTableName('wiki_revision');
        return <<<SQL
INNER JOIN {$wiki_revision_table_name} ON
    {$wiki_table_name}.`revision_id`  = {$wiki_revision_table_name}.`id`
SQL;
    }

   /**
     * @param int $id
     * @param int $language
     * @return string
     */
    private function buildWhereByIdClause($id, $language) {
        $wiki_table_name = $this->getTableName('wiki', $raw = true);
        return qsprintf(
            $this->connection,
            'WHERE %T.`id` = %d and %T.`language` = %d',
            $wiki_table_name, $id,
            $wiki_table_name, $language);
    }

    /**
     * @param string $name
     * @param int $parent_id
     * @return string
     */
    private function buildWhereByNameAndParentClause($name, $parent_id) {
        $wiki_table_name = $this->getTableName('wiki', $raw = true);
        return qsprintf(
            $this->connection,
            'WHERE %T.`name` = %s and %T.`parent_id` = %d',
            $wiki_table_name, $name,
            $wiki_table_name, $parent_id);
    }

    /**
     * @param int $revision_id
     * @return string
     */
    private function buildJoinByRevisionIdClause($revision_id) {
        $wiki_table_name = $this->getTableName('wiki', $raw = true);
        $wiki_revision_table_name =
            $this->getTableName('wiki_revision', $raw = true);
        return qsprintf(
            $this->connection,
            'INNER JOIN %T ON
                %T.`id` = %T.`wiki_id` and
                %T.`id` = %d',
            $wiki_revision_table_name,
            $wiki_table_name, $wiki_revision_table_name,
            $wiki_revision_table_name, $revision_id);
    }
}
