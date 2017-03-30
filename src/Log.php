<?php


namespace Jelmergu;


use Jelmergu\Exceptions\InvalidFileSystemRights;

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
        if (is_writable($newPath) === FALSE) {
            throw new InvalidFileSystemRights(222, $newPath);
        }
        self::$logLocation = $newPath;
    }

    protected static function writeLog($logName, $message, $extra)
    {
        $file = fopen(self::$logLocation . $logName.".log", "a");
        fwrite($file, self::prepareMessage($message));
        if (isset($extra[0][0]) === true){
            foreach ($extra[0] as $message) {
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

    private static function prepareMessage($file, string $message)
    {
        if (is_object($message) === TRUE || is_array($message)) {
            $message = json_encode($message);
        }
        $now = new DateTime();

        $message = "[" . $now->format("Y-m-d H:i:s:u") . "] " . $message . PHP_EOL;

        fwrite($file, $message);
    }

}