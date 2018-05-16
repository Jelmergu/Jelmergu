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
    public static $logLocation = "/";

    /**
     * Set the log location that gets used within this class
     *
     * @param string $path
     *
     * @return void
     */
    public static function setLogLocation(string $path)
    {
        $aPWD = preg_split("`/|\\\`", $path);
        $newPath = "";
        foreach ($aPWD as $sPWD) {
            if (strlen($sPWD) > 0) {
                $newPath .= $sPWD . DIRECTORY_SEPARATOR;
            }
        }

        self::$logLocation = $newPath;
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

        if (isset($extra[0][0]) === true){
            foreach ($extra[0] as $message) {

                if(Validator::objectOrArray($message) === true) {
                    $message = json_encode($message);
                }

                fwrite($file, self::prepareMessage($message));
            }
        }

        fclose($file);
    }


    /**
     * Write a message to the database log. Gets used a lot by \Jelmergu\Database
     *
     * @param       $message @see writeLog
     * @param mixed ...$extra @see writeLog
     *
     * @return void
     */
    public static function databaseLog($message, ...$extra)
    {
        if (isset($extra[0]) === true){
            self::writeLog("database", $message, $extra);
        }
        else {
            self::writeLog("database", $message);
        }
    }

    /**
     * Write a message to the debug log.
     *
     * @param       $message @see writeLog
     * @param mixed ...$extra @see writeLog
     *
     * @return void
     */
    public static function debugLog($message, ...$extra)
    {
        if (isset($extra[0]) === true){
            self::writeLog("debug", $message, $extra);
        }
        else {
            self::writeLog("debug", $message);
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
        return "[" . (new Date())->format("Y-m-d H:i:s:u") . "] " . $message . PHP_EOL;
    }

}
