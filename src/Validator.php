<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @since   1.0
 * @version 1.0.3
 * @todo    Correct version information in the docblocks
 * @package Jelmergu/Jelmergu
 */

namespace Jelmergu;

/**
 * This class contains multiple methods to check its input
 *
 * The class has methods that take multiple arguments to check values according to the input parameters
 *
 * @since   1.0
 * @version 1.0
 * @package Jelmergu/Jelmergu
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
     * @since   1.0
     * @version 1.0
     *
     * @param array $fields
     * @param array $indices
     *
     * @return bool
     */
    public static function areNumeric(array $fields, array $indices) : bool
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
     * Check if one of the supplied keys exists in the array
     *
     * @since   1.0.4
     * @version 1.0
     *
     * @param array $array The array to find a key in
     * @param array $keys  The keys which are to be searched for
     *
     * @return bool
     */
    public static function eitherKey(array $array, array $keys) : bool
    {
        foreach ($keys as $key) {
            if (isset($array[$key]) === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if one of values is equal to the input string
     *
     * @since   1.0.
     * @version 1.0
     *
     * @param string $field  The string to check
     * @param array  $values An array of allowed values
     *
     * @return bool
     */
    public static function either(string $field, array $values) : bool
    {
        foreach ($values as $key => $value) {

            if ($field == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * This method check if the specified $indices are set in the fields array
     *
     * @since   1.0
     * @version 1.0
     *
     * @param array $fields  This is the array that contains key => value pairs that have to be validated
     * @param array $indices This is the array that contains the keys that have to be set
     *
     * @return bool Returns true if all indices are set in the fields array
     */
    public static function areSet(array $fields, array $indices) : bool
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
     * @version 1.0
     * @since   1.0
     *
     * @param array $fields
     * @param array $indices
     *
     * @return bool
     */
    public static function areMixed(array $fields, array $indices) : bool
    {
        foreach ($indices as $key => $value) {
            if (isset($fields[$key]) === false) {
                return false;
            } elseif (self::is($fields[$key], $value) === false || $value == "") {
                return false;
            }
        }

        return true;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @todo    Change it to be shorter
     * @since   1.0
     * @version 1.0
     *
     * @param mixed  $field    The value of the field to check
     * @param string $constant One of the constants of self
     *
     * @return bool
     */
    public static function is($field, string $constant) : bool
    {
        switch ($constant) {
            case self::NUMERIC:
                return is_numeric($field) === true;
            break;
            case self::NOT_NUMERIC:
                return is_numeric($field) === false;
            break;
            case self::STRING:
                return is_string($field) === true;
            break;
            case self::NOT_STRING:
                return is_string($field) === false;
            break;
            case self::NULL:
                return is_null($field) === true;
            break;
            case self::NOT_NULL:
                return is_null($field) === false;
            break;
            case self::BOOL:
                return is_bool($field) === true;
            break;
            case self::NOT_BOOL:
                return is_bool($field) === false;
            break;
            case self::TRUE:
                return $field === true;
            break;
            case self::FALSE:
                return $field === false;
            break;
            case self::ARRAY:
                return is_array($field) === true;
            break;
            case self::NOT_ARRAY:
                return is_array($field) === false;
            break;
            case self::EMPTY:
                return empty($field) === true;
            break;
            case self::NOT_EMPTY:
                return empty($field) === false;
            break;
            default:
                if ($field == $constant) {
                    return true;
                }

                return false;
            break;
        }
    }

    /**
     * Check whether or not the input variable is either an object or an array
     *
     * @since   1.0.6
     * @version 1.0
     *
     * @param $var The variable to check
     *
     * @return bool
     */
    public static function objectOrArray($var)
    {
        return (is_array($var) === true || is_object($var) === true);
    }

    /**
     * Validate a email address
     *
     * @since   1.0.3
     * @version 1.0
     *
     * @param string $mailAddress The email adres to validate
     *
     * @return bool
     */
    public static function validateMail(string $mailAddress) : bool
    {
        if (strlen($mailAddress) > 254) {
            return false; // Mailadress is not allowed to be longer than 254 characters
        }

        $split = explode("@", $mailAddress);
        $domain = array_pop($split); // domain
        $local = implode("@", $split);

        if (self::validateLocal($local) && self::validateDomain($domain)) {
            return true;
        }

        return false;
    }

    /**
     * Validate the local part of an email address
     *
     * @unsupported Comments
     *
     * @since       1.0.3
     * @version     1.0
     *
     * @param string $local The local part of the email address
     *
     * @return bool
     */
    private static function validateLocal(string $local) : bool
    {

        if (strlen($local) >= 64) {
            return false; // local part is allowed to be up to 64 characters long
        }

        $quotedStringPart = '`^\"([\\\a-zA-Z0-9\.@\(\)<>\[\]:,;\"!#$\%&\-/=?^_\'\`{}| ~]{1,})\"$`';
        $unquotedStringPart = '`^([a-zA-Z0-9\`\'\`\-!#$%&*+/=?^_{|}~]{1,}(\.{1}|)){1,}$`';

        if (preg_match($quotedStringPart, $local) > 0) {
            return true;
        } elseif (strpos($local, '."') > 0 || strpos($local, '".') > 0) {
            $localParts = $local;
            if (strpos($local, '."') > 0) {
                $localParts = explode('."', $local);
                $preQuoute = $localParts[0];
                $localParts = '"' . $localParts[1];
            }
            if (strpos($local, '".') > 0) {
                $localParts = explode('".', $localParts);
                $postQuote = $localParts[1];
                $localParts = $localParts[0] . '"';
            }

            if (preg_match($quotedStringPart, $localParts) > 0) {
                if (isset($preQuoute) === true && preg_match($unquotedStringPart, $preQuoute) == 0) {
                    return false;
                } elseif (isset($postQuoute) === true && preg_match($unquotedStringPart, $postQuote) == 0) {
                    return false;
                }

                return true;
            }

            return false;
        } elseif (preg_match($unquotedStringPart, $local) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Validate the domain part of an email address
     *
     * @unsupported Comments
     *
     * @since       1.0.3
     * @version     1.0
     *
     * @param string $domain The domain of an email address
     *
     * @return bool
     */
    private static function validateDomain(string $domain) : bool
    {

        $domainParts = explode(".", $domain);
        foreach ($domainParts as $part) {
            if (strlen($part) > 64) {
                // every `subdomain` is allowed to be only 64 characters long
                return false;
            }
        }

        $ipRegex = '`\[([0-9\.]{0,4}){0,4}|(IPv6:([a-fA-F0-9:]{0,5}){1,8})\]$`';
        $domainRegex = "`^[a-zA-Z0-9\.\-]{1,245}$`";

        if (preg_match($ipRegex, $domain) > 0) {
            $domain = str_ireplace(["[", "]", "IPv6"], "", $domain);
            if (filter_var($domain, FILTER_VALIDATE_IP) === false) {
                return false;
            }
        } elseif (preg_match($domainRegex, $domain) == 0) {
            return false;
        }

        return true;
    }
}

