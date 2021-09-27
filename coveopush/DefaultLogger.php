<?php

namespace Coveo\Search\SDK\SDKPushPHP;
use Psr\Log\LoggerInterface;

class DefaultLogger implements LoggerInterface {

  /**
     * Whether the logger is enabled or not.
     *
     * @var bool
     */
    private static $isEnabled = true;

  /**
     * Disables the logger.
     */
    public static function disable()
    {
        self::$isEnabled = false;
    }

    /**
     * Enables the logger.
     */
    public static function enable()
    {
        self::$isEnabled = true;
    }

  function emergency($message, array $context = array()){
      error_log('emergency: '.$message, 0);
      $this->LogWindow('emergency: '.$message);
    }

    function alert($message, array $context = array()){
      error_log('alert: '.$message, 0);
      $this->LogWindow('alert: '.$message);
    }

     function critical($message, array $context = array()){
      error_log('Critical: '.$message, 0);
      $this->LogWindow('Critical: '.$message);
     }

    function error($message, array $context = array()){
      error_log('Error: '.$message, 0);
      $this->LogWindow('Error: '.$message);
    }

     function warning($message, array $context = array()){
        error_log('Warning: '.$message, 0);
        $this->LogWindow('Warning: '.$message);
     }

    function notice($message, array $context = array()){
      error_log('Notice: '.$message, 0);
      $this->LogWindow('Notice: '.$message);
     }

    function info($message, array $context = array()){
      error_log('Info: '.$message, 0);
      $this->LogWindow('Info: '.$message);
     }

    function debug($message, array $context = array()){
      error_log('Debug: '.$message, 0);
      $this->LogWindow('Debug: '.$message);
     }

     function log($level, $message, array $context = array()){
      error_log('Log: '.$message, 0);
      $this->LogWindow('Log: '.$message);
     }

     function LogWindow( $err){
      if (self::$isEnabled) {
        echo "</BR>";
        echo $err;
      }
    }
}
