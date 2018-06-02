<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @since     1.0.6
 * @version   1.0
 *
 * @package   Jelmergu/Jelmergu
 */


namespace Jelmergu\Exceptions;

use Jelmergu\Log;

/**
 * This is a wrapper around the native PHP Exception, adding some logging capabilities
 *
 * @package Jelmergu\Exceptions
 */
abstract class Exception extends \Exception
{

    public $prefix;

    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        $this->setPrefix();
        parent::__construct($message, $code, $previous);
    }

    abstract protected function setPrefix();

    /**
     * Write the exception to a log file
     * @since 1.0.6
     * @version 1.0.1
     *
     * @return void
     */
    public function toLog()
    {
        Log::writeLog(
            "error",
            $this->prefix . " at " . $this->getFile() . ":" . $this->getLine() . PHP_EOL .
            "\t " . $this->getMessage() . PHP_EOL .
            "\t " . json_encode($this->getTrace()) . PHP_EOL
        );
    }
}