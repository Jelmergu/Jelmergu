<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version v1.0
 *
 * @package Jelmergu
 */

namespace Jelmergu;

/**
 * This class contains multiple methods to check its input
 *
 * The class has methods that take multiple arguments to check values according to the input parameters
 *
 * @version v1.0
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
    const ARRAY       = "array";
    const NOT_ARRAY   = "!array";
    const EMPTY       = "empty";
    const NOT_EMPTY   = "!empty";

    /**
     * This method check if the specified $indices are set and numeric in the fields array
     *
     * @param array $fields
     * @param array $indices
     *
     * @version v1.0
     *
     * @return bool
     */
    public static function areNumeric(array $fields, array $indices) : bool
    {
        if (self::areSet($fields, $indices) === TRUE) {
            foreach ($indices as $index) {
                if (is_numeric($fields[$index]) === FALSE) {
                    return FALSE;
                }
            }

            return TRUE;
        }

        return FALSE;
    }

    public static function either(string $field, array $values) : bool {

        foreach ($values as $key => $value) {
            if ($field == $value){
                return true;
            }
        }
        return false;
    }

    /**
     * This method check if the specified $indices are set in the fields array
     *
     * @param array $fields  This is the array that contains key => value pairs that have to be validated
     * @param array $indices This is the array that contains the keys that have to be set
     *
     * @version v1.0
     *
     * @return bool Returns true if all indices are set in the fields array
     */
    public static function areSet(array $fields, array $indices) : bool
    {
        foreach ($indices as $index) {
            if (isset($fields[$index]) === FALSE) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @param array $fields
     * @param array $indices
     *
     * @version v1.0
     *
     * @return bool
     */
    public static function areMixed(array $fields, array $indices) : bool
    {
        foreach ($indices as $key => $value) {
            if (isset($fields[$key]) === FALSE) {
                return FALSE;
            }
            elseif (self::is($fields[$key], $value) === FALSE) {
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @param mixed  $field    The value of the field to check
     * @param string $constant One of the constants of self
     *
     * @version v1.0
     *
     * @return bool
     */
    public static function is($field, string $constant) : bool
    {
        switch ($constant) {
            case self::NUMERIC:
                return is_numeric($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_NUMERIC:
                return is_numeric($field) === FALSE ? TRUE : FALSE;
                break;
            case self::STRING:
                return is_string($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_STRING:
                return is_string($field) === FALSE ? TRUE : FALSE;
                break;
            case self::NULL:
                return is_null($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_NULL:
                return is_null($field) === FALSE ? TRUE : FALSE;
                break;
            case self::BOOL:
                return is_bool($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_BOOL:
                return is_bool($field) === FALSE ? TRUE : FALSE;
                break;
            case self::TRUE:
                return $field === TRUE ? TRUE : FALSE;
                break;
            case self::FALSE:
                return $field === FALSE ? TRUE : FALSE;
                break;
            case self::ARRAY:
                return is_array($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_ARRAY:
                return is_array($field) === FALSE ? TRUE : FALSE;
                break;
            case self::EMPTY:
                return empty($field) === TRUE ? TRUE : FALSE;
                break;
            case self::NOT_EMPTY:
                return empty($field) === FALSE ? TRUE : FALSE;
                break;
        }

        return FALSE;
    }
}