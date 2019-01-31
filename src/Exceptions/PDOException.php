<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @since     1.0.6
 * @version   1.0.1
 *
 * @package   Jelmergu/Jelmergu
 */

namespace Jelmergu\Exceptions;

/**
 * This is a override for PDOException
 *
 * This exception can be thrown by the database trait
 * and should point to the correct file and line number that the trait's method was called on
 *
 */
class PDOException extends \PDOException
{
    /**
     * PDOException constructor.
     * Override to skip the trait methods and go to the calling line
     *
     * @since 1.0.6
     * @version 1.0
     *
     * @param string         $message
     * @param string|int     $code
     * @param Throwable|NULL $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message);
        $this->code = $code;
        try {
            $DatabaseReflection = new \ReflectionClass("Jelmergu\Database");
            $backtrace          = $this->getTrace();

            // Go through the backtrace until a function is found that is not a method of Jelmergu\Database
            foreach ($backtrace as $key => $caller) {
                if ($DatabaseReflection->hasMethod($caller['function']) === false) {
                    $this->file = $backtrace[$key - 1]['file'];
                    $this->line = $backtrace[$key - 1]['line'];
                    break;
                }
            }
        }
        catch (\ReflectionException $e) {
            $e->getMessage();
        }
    }
}