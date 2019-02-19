<?php


namespace Jelmergu\Exceptions;


class ConstantNotSetException extends Exception
{
    protected function setPrefix()
    {
        $this->prefix = "ConstantNotSet";
    }

    public function __construct(string $message = "", int $code = 0, \Exception $previous = null)
    {
        $message = "Constant ".$message." was not set";
        parent::__construct($message, $code, $previous);
    }

}