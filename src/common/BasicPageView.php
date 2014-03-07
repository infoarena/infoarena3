<?php

/**
 * Very much inspired from AphrontPageView from Phabricator
 */
abstract class BasicPageView extends BasicView {
    private $title;

    /**
     * Sets the title of the current page
     *
     * @param string $new_title
     * @return $this
     * @throws ParameterException
     */
    final public function setTitle($new_title) {
        if (!is_string($new_title)) {
            throw new ParameterException(
                get_class($this)."::setTitle expects `new_title` to be a ".
                "string");
        }
        $this->title = $new_title;
        return $this;
    }

    /**
     * Returns the title of the current page
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    public function render() {
        $title = $this->getTitle();
        $head = $this->getHead();
        $body = $this->getBody();
        $footer = $this->getFooter(); // lazy scrips

        $body_class = $this->getBodyClass(); // extra formatting

        $body = phutil_tag(
            'body',
            array(
                'class' => $body_class),
            array($body, $footer));

        return hsprintf(
            '<!DOCTYPE html>'.
            '<html>'.
              '<head>'.
                '<meta charset="UTF-8" />'.
                '<title>%s</title>'.
                '%s'.
              '</head>'.
              '%s'.
            '</html>',
            $title, $head, $body);
    }

    /**
     * Returns the information that should be added to the <head> tag
     * Things like css or meta information end up here
     *
     * @return string
     */
    protected function getHead() {
        return '';
    }

    /**
     * Returns the information that should be added into the <body> tag
     * The content of the website should be here
     *
     * @return string
     */
    protected function getBody() {
        return '';
    }

    /**
     * Returns the information that should be added right before the close
     * </body> tag
     * Things like scripts (jquery) should be added here
     *
     * @return string
     */
    protected function getFooter() {
        return '';
    }

    /**
     * Returns the classes that should be associated to the body tag
     * Defaults to null because phutil_tag doesn't add fields with null values
     *
     * @return string
     */
    protected function getBodyClass() {
        return null;
    }
}
