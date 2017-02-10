<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version
 *
 * @package Jelmergu
 */

namespace Jelmergu\Exceptions;


class FileNotFound extends \Exception
{
    /**
     * Write the exception to a log file
     *
     * @return void
     */
    public function toLog()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT']."/error.log") === true) {
            $errorFile = fopen($_SERVER['DOCUMENT_ROOT']."/error.log", "a");
        }
        else {
            echo "create";
            $errorFile = fopen($_SERVER['DOCUMENT_ROOT']."../error.log", "c");
        }
        $now = new \DateTime(null);
        fwrite(
            $errorFile,
            "[".$now->format("Y-m-d H:i:s u")."] FileNotFound at ".$this->file.":".$this->line.PHP_EOL.
            "\t Missing file: ".$this->message.PHP_EOL.
            "\t ". json_encode($this->getTrace()) . PHP_EOL
        );
        fclose($errorFile);
    }
}