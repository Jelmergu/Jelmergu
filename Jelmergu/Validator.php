<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version 0.1
 *
 * @package Jelmergu
 */

namespace Jelmergu;

/**
 * This class contains multiple methods to check its input
 *
 * The class has methods that take multiple arguments to check values according to the input parameters
 *
 * @version 0.1
 * @package Jelmergu
 */
class Validator
{

    const NUMERIC     = "number";
    const NOT_NUMERIC = "!number";
    const STRING      = "string";
    const NOT_STRING  = "!string";
    const NULL        = "null";
    const NOT_NULL    = "!null";
    const BOOL        = "bool";
    const NOT_BOOL    = "!bool";
    const TRUE        = "true";
    const FALSE       = "false";

    /**
     * This method check if the specified $indices are set and numeric in the fields array
     *
     * @param array $fields
     * @param array $indices
     *
     * @version 0.1
     *
     * @return bool
     */
    public static function areNumeric(array $fields, array $indices): bool
    {
        if (self::areSet($fields, $indices) === true) {
            foreach ($indices as $index) {
                if (is_numeric($fields[$index]) === false) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * This method check if the specified $indices are set in the fields array
     *
     * @param array $fields  This is the array that contains key => value pairs that have to be validated
     * @param array $indices This is the array that contains the keys that have to be set
     *
     * @version 0.1
     *
     * @return bool Returns true if all indices are set in the fields array
     */
    public static function areSet(array $fields, array $indices): bool
    {
        foreach ($indices as $index) {
            if (isset($fields[$index]) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @param array $fields
     * @param array $indices
     *
     * @version 0.1
     *
     * @return bool
     */
    public static function areMixed(array $fields, array $indices): bool
    {
        foreach ($indices as $key => $value) {
            if (isset($fields[$key]) === true) {
                if (self::isMixed($fields[$key]) === false) {
                    return false;
                }
            }
        }
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @param mixed  $field    The value of the field to check
     * @param string $constant One of the constants of self
     *
     * @version 0.1
     *
     * @return bool
     */
    private static function isMixed($field, string $constant): bool
    {
        switch ($constant) {
            case self::NUMERIC:
                if (is_numeric($field) === false) {
                    return false;
                }
                break;
            case self::NOT_NUMERIC:
                if (is_numeric($field) === true) {
                    return false;
                }
                break;
            case self::STRING:
                if (is_string($field) === false) {
                    return false;
                }
                break;
            case self::NOT_STRING:
                if (is_string($field) === true) {
                    return false;
                }
                break;
            case self::NULL:
                if (is_null($field) === false) {
                    return false;
                }
                break;
            case self::NOT_NULL:
                if (is_null($field) === true) {
                    return false;
                }
                break;
            case self::BOOL:
                if (is_bool($field) === false) {
                    return false;
                }
                break;
            case self::NOT_BOOL:
                if (is_bool($field) === true) {
                    return false;
                }
                break;
            case self::TRUE:
                if ($field === false) {
                    return false;
                }
                break;
            case self::FALSE:
                if ($field === true) {
                    return false;
                }
                break;
        }
    }
}