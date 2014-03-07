<?php

/**
 * This way we can catch accidental sets to inexistent fields
 * Similar to Phobject
 */
abstract class SafeObject {
  /**
   * @param string value
   * @param mixed value
   * @throws UnsafeSetException
   */
  public function __set($name, $value) {
    throw new UnsafeSetException(
      "Attempted write to undeclared property ".get_class($this)."::\$$name. ");
  }
}
