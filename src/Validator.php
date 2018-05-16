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

    const NUMERIC     = "is_numeric";
    const STRING      = "is_string";
    const NULL        = "is_null";
    const BOOL        = "is_bool";
    const ARRAY       = "is_array";
    const EMPTY       = "is_empty";
    const OBJECT      = "is_object";
    const NOT_NUMERIC = "!is_numeric";
    const NOT_STRING  = "!is_string";
    const NOT_NULL    = "!is_null";
    const NOT_BOOL    = "!is_bool";
    const NOT_ARRAY   = "!is_array";
    const NOT_EMPTY   = "!is_empty";
    const NOT_OBJECT  = "!is_object";
    const TRUE        = "true";
    const FALSE       = "false";

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
     * Note: Validator considers the strings "" and '' as not a string, for an empty string is not a string of
     * characters
     *
     * @version 1.0.1
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
            } elseif (isset($fields[$value]) === false) {
                /*
                 * Check for $fields[$value] is to make it possible to do something like
                 *   Validator::areMixed($fields, [0, 1 => Validator::NOT_EMPTY]);
                 */
                if ($value != self::EMPTY && $fields[$key] == "") {
                    return false;
                } elseif (self::is($fields[$key], $value) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @since   1.0
     * @version 1.0.1
     *
     * @param mixed  $field    The value to check
     * @param string $constant One of the constants of self, an other function name with one argument or as string to
     *                         perform lose comparison on
     *
     * @return bool
     */
    public static function is($field, string $constant) : bool
    {
        $constants = (new \ReflectionClass(self::class))->getConstants();
        if (in_array($constant, $constants)) {
            if ($constant === self::FALSE || $constant === self::TRUE) {
                return $constant == self::FALSE ? $field === false : $field === true;
            } else {
                $value = true;
                if ($constant[0] == "!") {
                    $value = false;
                    $constant = str_split($constant);
                    unset($constant[0]);
                    $constant = implode($constant);
                }
                if ($constant == self::EMPTY) {
                    return empty($field) === $value;
                }

                return $constant($field) === $value;
            }
        } elseif (\function_exists($constant) === true) {
            return (bool)$constant($field);
        } else {
            return $constant == $field;
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
                $preQuote = $localParts[0];
                $localParts = '"' . $localParts[1];
            }
            if (strpos($local, '".') > 0) {
                $localParts = explode('".', $localParts);
                $postQuote = $localParts[1];
                $localParts = $localParts[0] . '"';
            }

            if (preg_match($quotedStringPart, $localParts) > 0) {
                if (isset($preQuote) === true && preg_match($unquotedStringPart, $preQuote) == 0) {
                    return false;
                } elseif (isset($postQuote) === true && preg_match($unquotedStringPart, $postQuote) == 0) {
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
     * @version     1.0.1
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
        if (preg_match("`\[{1}`", $domain) == 1 && preg_match("`\]{1}`", $domain) == 1) {

            $domainRegex = "`(?:\[{1})([0-9.]{7,15})(?:\]{1})|(?:\[{1})(?:IPv6:)([0-9a-zA-Z:]{3,24})(?:\]{1})`";
            if (preg_match($domainRegex, $domain, $matches) > 0) {
                if (isset($matches[0]) === true) {
                    if (filter_var($matches[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4|FILTER_FLAG_IPV6) === false) {
                        return false;
                    }
                }

                return true;
            }
        }
        elseif (preg_match("`^[a-z0-9]{1}[a-z0-9\-.]*[a-z0-9]{1}$`i", $domain) == 1) {
            return true;
        }
        return false;
    }
}

