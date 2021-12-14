<?php

namespace Coveo\Search\SDK\SDKPushPHP;

use Psr\Log\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Default PSR Logger.
 */
class DefaultLogger implements LoggerInterface {

  /**
   * Whether the logger is enabled or not.
   *
   * @var bool
   */
  private static $isEnabled = TRUE;

  /**
   * Disables the logger.
   */
  public static function disable() {
    self::$isEnabled = FALSE;
  }

  /**
   * Enables the logger.
   */
  public static function enable() {
    self::$isEnabled = TRUE;
  }

  /**
   * Log emergency type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function emergency($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::EMERGENCY . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log alert type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function alert($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::ALERT . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

   /**
   * Log critical type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function critical($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::CRITICAL . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log error type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function error($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::ERROR . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log warning type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function warning($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::WARNING . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log notice type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function notice($message, array $context = array()){
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::NOTICE . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log info type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function info($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::INFO . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log debug type messages.
   *
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function debug($message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    $level = LogLevel::DEBUG . ': ';
    error_log($level . $message, 0);
    $this->LogWindow($level . $message);
  }

  /**
   * Log type messages.
   *
   * @param Psr\Log\LogLevel $level
   *   Level.
   * @param string $message
   *   Message to log.
   * @param mixed[] $context
   *   Context.
   */
  function log($level, $message, array $context = array()) {
    if (!self::$isEnabled) {
      return;
    }
    switch ($level) {
      case LogLevel::EMERGENCY:
        $this->emergency($message, $context);
        break;

      case LogLevel::ALERT:
        $this->alert($message, $context);
        break;

      case LogLevel::CRITICAL:
        $this->critical($message, $context);
        break;

      case LogLevel::ERROR:
        $this->error($message, $context);
        break;

      case LogLevel::WARNING:
        $this->warning($message, $context);
        break;

      case LogLevel::NOTICE:
        $this->notice($message, $context);
        break;

      case LogLevel::INFO:
        $this->info($message, $context);
        break;

      case LogLevel::DEBUG:
        $this->debug($message, $context);
        break;

      default:
        throw new InvalidArgumentException("Unknown severity level");
    }
  }

  function LogWindow($err) {
    if (self::$isEnabled) {
      echo "</BR>";
      echo $err;
    }
  }

}
