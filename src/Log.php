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


    public static function databaseLog($message, ...$extra)
    {
        if (isset($extra[0]) === true){
            self::writeLog("database", $message, $extra);
        }
        else {
            self::writeLog("database", $message);
        }
    }

    public static function debugLog($message, ...$extra)
    {
        if (isset($extra[0]) === true){
            self::writeLog("debug", $message, $extra);
        }
        else {
            self::writeLog("debug", $message);
        }
    }

    private static function prepareMessage(string $message)
    {
        return "[" . (new Date())->format("Y-m-d H:i:s:u") . "] " . $message . PHP_EOL;
    }

}
