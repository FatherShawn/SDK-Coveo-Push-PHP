<?php

namespace Coveo\Search\SDK\SDKPushPHP;

/**
 * Implements the abstract base for all enum types
 * @see http://stackoverflow.com/a/2324746/1003020
 * @see http://stackoverflow.com/a/254543/1003020
 *
 * Example of a typical enum:
 *
 *    class DayOfWeek extends Enum
 *    {
 *        const Sunday    = 0;
 *        const Monday    = 1;
 *        const Tuesday   = 2;
 *        const Wednesday = 3;
 *        const Thursday  = 4;
 *        const Friday    = 5;
 *        const Saturday  = 6;
 *    }
 *
 * Usage examples:
 *
 *     $monday = DayOfWeek::Monday                      // (int) 1
 *     DayOfWeek::isValidName('Monday')                 // (bool) TRUE
 *     DayOfWeek::isValidName('monday', $strict = TRUE) // (bool) FALSE
 *     DayOfWeek::isValidValue(0)                       // (bool) TRUE
 *     DayOfWeek::fromString('Monday')                  // (int) 1
 *     DayOfWeek::toString(DayOfWeek::Tuesday)          // (string) "Tuesday"
 *     DayOfWeek::toString(5)                           // (string) "Friday"
 */
abstract class Enum {
  /**
   * Constants cache.
   *
   * @var array
   */
  private static $constCacheArray = NULL;

  /**
   * Get Constants.
   *
   * @return array
   */
  private static function getConstants() {
    if (self::$constCacheArray == NULL) {
      self::$constCacheArray = [];
    }
    $calledClass = get_called_class();
    if (!array_key_exists($calledClass, self::$constCacheArray)) {
      $reflect = new \ReflectionClass($calledClass);
      self::$constCacheArray[$calledClass] = $reflect->getConstants();
    }
    return self::$constCacheArray[$calledClass];
  }

  /**
   * Is valid name.
   *
   * @param string $name
   *   Constant name.
   * @param bool $strict
   *   Strict comparison.
   *
   * @return bool
   */
  public static function isValidName($name, $strict = FALSE) {
    $constants = self::getConstants();

    if ($strict) {
      return array_key_exists($name, $constants);
    }

    $keys = array_map('strtolower', array_keys($constants));
    return in_array(strtolower($name), $keys);
  }

  /**
   * Is Valid Value.
   *
   * @param string $value
   * @param bool $strict
   * @return bool
   */
  public static function isValidValue($value, $strict = TRUE) {
    $values = array_values(self::getConstants());
    return in_array($value, $values, $strict);
  }

  public static function fromString($name) {
    if (self::isValidName($name, $strict = TRUE)) {
      $constants = self::getConstants();
      return $constants[$name];
    }
    return FALSE;
  }

  public static function toString($value) {
    if (self::isValidValue($value, $strict = TRUE)) {
      return array_search($value, self::getConstants());
    }
    return FALSE;
  }

}