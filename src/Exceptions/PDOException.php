<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   1.0.6
 *
 * @package   Jelmergu
 */
namespace Jelmergu\Exceptions;

use Throwable;

/**
 * This is a override for PDOException
 *
 * This exception can be thrown by the database trait
 * and will point to the correct file and line number that the trait's method was called on
 *
 * @package Jelmergu
 */
class PDOException extends \PDOException
{
    /**
     * PDOException constructor.
     * Override to skip the trait methods and go to the calling line
     *
     * @version 1.0.6
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|NULL $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        parent::__construct($message, $code, $previous);
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[3];
        $this->file = $caller['file'];
        $this->line = $caller['line'];
    }
}