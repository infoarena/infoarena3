<?php

/**
 * We're trying to emulate XHP
 * BasicView is the base for all of our elements
 */
abstract class BasicView extends SafeObject
    implements PhutilSafeHTMLProducerInterface {

    protected $children = array();

    /**
     * Append the given child
     *
     * Will throw exception if you try to append a child to an element that
     * does not support this operation
     *
     * @param BasicView $child
     * @return $this
     * @throws ViewException
     */
    final public function appendChild(BasicView $child) {
        if (!$this->canHaveChildren()) {
            throw new ViewException(
                "View ".get_class($this)." does not support children");
        }

        $this->children[] = $child;
        return $this;
    }

    /**
     * Returns the children of the current element
     * @return array[BasicView]
     */

    final public function getChildren() {
        return $this->children;
    }

    /**
     * Render the current element
     * @return string
     */
    abstract public function render();

    /**
     * To match the PhutilSafeHTMLProducerInterface
     * @return string
     */
    final public function producePhutilSafeHTML() {
        return $this->render();
    }

   /**
     * If we want to make "HTML" objects that can not have their content
     * edited we can make this function return false
     *
     * @return bool
     */
    protected function canHaveChildren() {
        return true;
    }
}
