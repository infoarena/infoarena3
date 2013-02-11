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

        $result = $this->connection->query_one(
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

        $result = $this->connection->query_one(
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

        $result = $this->connection->query_one(
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
        return <<<SQL
            SELECT
                `ia_wiki`.`id` as `id`,
                `ia_wiki`.`name` as `name`,
                `ia_wiki`.`language` as `language`,
                `ia_wiki`.`creation_timestamp` as `creation_timestamp`,
                `ia_wiki`.`owner` as `owner`,
                `ia_wiki`.`security` as `security`,
                `ia_wiki_revision`.`id` as `revision_id`,
                `ia_wiki_revision`.`text` as `text`,
                `ia_wiki_revision`.`description` as `description`,
                `ia_wiki_revision`.`timestamp` as `timestamp`,
                `ia_wiki_revision`.`editor` as `editor`,
            FROM `ia_wiki`
SQL;
    }

    /**
     * @return string
     */
    private function buildSelectIdClause() {
        return <<<SQL
            SELECT
                `ia_wiki`.`id` as `id`
            FROM `ia_wiki`
SQL;
    }

    /**
     * @return string
     */
    private function buildJoinClause() {
        return <<<SQL
             INNER JOIN `ia_wiki_revision` ON
                `ia_wiki`.`revision_id`  = `ia_wiki_revision`.`id`
SQL;
    }

   /**
     * @param int $id
     * @param int $language
     * @return string
     */
    private function buildWhereByIdClause($id, $language) {
        return qsprintf(
            $this->connection,
            'WHERE `ia_wiki`.`id` = %d and `ia_wiki`.`language` = %d',
            $id, $language);
    }

    /**
     * @param string $name
     * @param int $parent_id
     * @return string
     */
    private function buildWhereByNameAndParentClause($name, $parent_id) {
        return qsprintf(
            $this->connection,
            'WHERE `ia_wiki`.`name` = %s and `ia_wiki`.`parent_id` = %d',
            $name, $parent_id);
    }

    /**
     * @param int $revision_id
     * @return string
     */
    private function buildJoinByRevisionIdClause($revision_id) {
        return qsprintf(
            $this->connection,
            'INNER JOIN `ia_wiki_revision` ON
                `ia_wiki`.`id` = `ia_wiki_revision`.`wiki_id` and
                `ia_wiki_revision`.`id` = %d',
            $revision_id);
    }
}
