<?php
class CbLogger
{

    const PRIORITY_DEBUG  = 1;
    const PRIORITY_INFO   = 2;
    const PRIORITY_WARN   = 3;
    const PRIORITY_ERROR  = 4;
    const PRIORITY_DEFAULT  = 99;

    protected static $priorities = array(
        self::PRIORITY_DEBUG => 'DEBUG',
        self::PRIORITY_INFO => 'INFO',
        self::PRIORITY_WARN => 'WARN',
        self::PRIORITY_ERROR => 'ERROR',
        self::PRIORITY_DEFAULT => 'DEFAULT'
    );

    protected static $logLevel = -1;

    protected static $logFile;

    public static function setLogFile($filename)
    {
        self::$logFile = fopen($filename, 'w+');
    }

    public static function setLogLevel($priority)
    {
        self::$logLevel = $priority;
    }

    public static function log($message, $priority = CbLogger::PRIORITY_INFO)
    {
        if ($priority < self::$logLevel) {
            return;
        }
        $message = sprintf(
            '%s - %s: %s',
            date('Y-m-d h:i:s'),
            self::$priorities[$priority],
            $message
        );
        echo $message.PHP_EOL;
        if (self::$logFile) {
            fwrite(self::$logFile, $message.PHP_EOL);
        }
    }

    public function __destruct()
    {
        if (self::$logFile) {
            fclose(self::$logFile);
        }
    }

}