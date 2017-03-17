<?php

namespace Jelmergu\Exceptions;
use Jelmergu\Date;

abstract class Exception extends \Exception
{

    public $prefix;

    public function __construct($message = "", $code = 0, \Exception $previous = NULL)
    {
        $this->setPrefix();
        parent::__construct($message, $code, $previous);
    }

    abstract protected function setPrefix();

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
        $now = new Date();
        fwrite(
            $errorFile,
            "[".$now->format("Y-m-d H:i:s u")."] ".$this->prefix . " at ".$this->file.":".$this->line.PHP_EOL.
            "\t ".$this->message.PHP_EOL.
            "\t ". json_encode($this->getTrace()) . PHP_EOL
        );
        fclose($errorFile);
    }
}