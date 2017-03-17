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
        $aPWD = preg_split("`\\|/`", $path);
        $newPath = "";
        foreach ($aPWD as $sPWD) {
            $newPath .= $sPWD . DIRECTORY_SEPARATOR;
        }
        if (is_writable($newPath) === false) {
            throw new InvalidFileSystemRights(222, $newPath);
        }
        self::$logLocation = $newPath;
    }

    public static function DatabaseLog($message)
    {
        $file = fopen(self::$logLocation."Database.log", "a");
        fwrite($file, $message);
        fclose($file);
    }

    private static function prepareMessage($message)
    {
        if (is_object($message) === TRUE || is_array($message)) {
            $message = json_encode($message);
        }
        $now = new DateTime();

        $message = "[" . $now->format("Y-m-d H:i:s:u") . "] " . $message . PHP_EOL;

        return $message;
    }

}