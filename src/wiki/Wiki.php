<?php

/**
 *
 */
final class Wiki {
    private $id;
    private $revisionId;
    private $name;
    private $text;
    private $language;
    private $description;
    private $timestamp;
    private $creationTimestamp;
    private $owner;
    private $editor;
    private $security;

    /**
     * @param array $dictionary
     * @return void
     */
    public function __construct(array $dictionary) {
        $this->id = (int)$dictionary['id'];
        $this->revisionId = (int)$dictionary['revision_id'];
        $this->name = (string)$dictionary['name'];
        $this->text = (string)$dictionary['text'];
        $this->language = Language::interpret($dictionary['language']);
        $this->description = (string)$dictionary['description'];
        $this->timestamp = strtotime($dictionary['timestamp']);
        $this->creationTimestamp  =
            strtotime($dictionary['creation_timestamp']);
        if ($this->timestamp === false || $this->creationTimestamp === false) {
            throw new WikiParameterException(
                "Wiki timestamp unrecocgnized");
        }
        $this->owner = (int)$dictionary['owner'];
        $this->editor = (int)$dictionary['editor'];
        $this->security =
            WikiSecurityLevel::interpret($dictionary['security']);
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRevisionId() {
        return $this->revisionId;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Just in case you need to alter the text of the wiki while formatting
     *
     * @param string $text
     * @return Wiki         returns the current object so you can link calls
     */
    public function setText($text) {
        if (!is_string($text)) {
            throw new WikiParameterException("
                Wiki::setText expects `text` to be a string");
        }

        $this->text = $text;
        return $this;
    }

    /**
     * @return int
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @return int   UNIX time
     */
    public function getTimestamp() {
        return $this->timestamp;
    }

    /**
     * Just in case you need to alter the timestamp while formatting
     * @param string $timestamp
     * @return Wiki              returns the current object so you can link
     *                          calls
     */
    public function setTimestamp($timestamp) {
        if (!is_int($timestamp)) {
            throw new WikiParameterException("
                Wiki::setTimestamp expects `timestamp` to be an int");
        }

        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreationTimestamp($creation_timestamp) {
        return $this->creationTimestamp;
    }

    /**
     * @return int
     */
    public function getOwnerId() {
        return $this->owner;
    }

    /**
     * return int
     */
    public function getLastEditor() {
        return $this->editor;
    }

    /**
     * return int
     */
    public function getSecurityLevel() {
        return $this->security;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isName($name) {
        if (!is_string($name)) {
            throw new WikiParameterException(
                'Wiki::isName expects `name` to be a string');
        }

        if (strlen($name) < 1 || strlen($name) > 64) {
            return false;
        }

        return preg_match('/^[a-z0-9][a-z0-9_\-\.]*$/i', $name) > 0;
    }
}
