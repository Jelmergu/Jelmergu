<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version 1.0
 * @since   1.0.6
 * @todo Add DocBlock for each method
 * @package Jelmergu/Jelmergu
 */

namespace Jelmergu;


class Log
{

    /**
     * @var string Relative or absolute path to the folder where the logs are located
     */
    public static $logLocation = __DIR__;

    /**
     * Set the log location that gets used within this class
     *
     * @param string $path
     *
     * @return void
     */
    public static function setLogLocation(string $path)
    {
        if (!is_dir($path)) {
            throw new \Jelmergu\Exceptions\FileNotFound("Given path is not a valid directory: path given='${path}'");
        }
        if (substr($path, -1) != DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        self::$logLocation = $path;
    }

    /**
     * Write one or more messages to the specified log
     *
     * @param       $logName  The name of a log, without extention
     * @param       $message  The message to write
     * @param mixed ...$extra Extra messages to write
     *
     * @return void
     */
    public static function writeLog($logName, $message, ...$extra)
    {
        if(Validator::objectOrArray($message) === true) {
            $message = json_encode($message);
        }

        $file = fopen(self::$logLocation . $logName.".log", "a");
        fwrite($file, self::prepareMessage($message));

        if (isset($extra[0]) === true){
            foreach ($extra as $message) {
                if(Validator::objectOrArray($message) === true) {
                    $message = json_encode($message);
                }
                fwrite($file, self::prepareMessage($message));
            }
        }

        fclose($file);
    }


    /**
     * Allows for calls of Log::databaseLog
     *
     * @param $name
     * @param $arguments
     *
     * @return void
     */
    public static function __callStatic($name, $arguments)
    {
        if (substr($name, -3, 3) == "Log") {
            $log = preg_replace("`Log$`", "", $name);
            array_unshift($arguments, $log);
            call_user_func_array([self::class, "writeLog"], $arguments);
        }
    }

    /**
     * Prepare the message to be written to a log. Adds a heading with the current timestamp.
     *
     * @param string $message The message that will be written
     *
     * @return string The message with a heading with the current timestamp and a new line
     */
    private static function prepareMessage(string $message)
    {
        return "[" . (new Date())->format("Y-m-d H:i:s.u") . "] " . $message . PHP_EOL;
    }

}
