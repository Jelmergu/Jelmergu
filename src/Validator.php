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
     * @param array $fields
     * @param array $indices
     *
     * @return bool
     * @version 1.0
     *
     * @since   1.0
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
     * @param array $array The array to find a key in
     * @param array $keys  The keys which are to be searched for
     *
     * @return bool
     * @version 1.0
     *
     * @since   1.0.4
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
     * @param string $field  The string to check
     * @param array  $values An array of allowed values
     *
     * @return bool
     * @version 1.0
     *
     * @since   1.0.
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
     * @param array $fields  This is the array that contains key => value pairs that have to be validated
     * @param array $indices This is the array that contains the keys that have to be set
     *
     * @return bool Returns true if all indices are set in the fields array
     * @version 1.0
     *
     * @since   1.0
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
     * @param array $haystack
     * @param array $needles
     *
     * @return bool
     * @since   1.0
     *
     * @version 1.0.1
     */
    public static function areMixed(array $haystack, array $needles) : bool
    {
        foreach ($needles as $key => $value) {
            if (isset($haystack[$key]) === false) {
                return strpos($value, "!") !== false;
            } elseif (isset($haystack[$value]) === false) {
                /*
                 * Check for $fields[$value] is to make it possible to do something like
                 *   Validator::areMixed($fields, [0, 1 => Validator::NOT_EMPTY]);
                 */
                if ($value != self::EMPTY && $haystack[$key] == "") {
                    return false;
                } elseif (self::is($haystack[$key], $value) === false) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * This method checks if the specified indices contain the specified type in the fields array
     *
     * @param mixed  $field    The value to check
     * @param string $constant One of the constants of self, an other function name with one argument or as string to
     *                         perform lose comparison on
     *
     * @return bool
     * @version 1.0.1
     *
     * @since   1.0
     */
    public static function is($field, string $constant) : bool
    {
        $constants = (new \ReflectionClass(self::class))->getConstants();
        $functionNameStripped = str_replace("!", "", $constant);
        if (in_array($constant, $constants)) {
            if ($constant === self::FALSE || $constant === self::TRUE) {
                return $constant == self::FALSE ? $field === false : $field === true;
            } else {
                $value = true;
                if ($constant[0] == "!") {
                    $value    = false;
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
        } elseif (\function_exists($functionNameStripped) === true) {
            return !((bool)$functionNameStripped($field));
        } else {
            var_dump($constant, \function_exists($constant) === true);
            return $constant == $field;
        }
    }

    /**
     * Check whether or not the input variable is either an object or an array
     *
     * @param $var The variable to check
     *
     * @return bool
     * @since   1.0.6
     * @version 1.0
     *
     */
    public static function objectOrArray($var)
    {
        return (is_array($var) === true || is_object($var) === true);
    }

    /**
     * Validate a email address
     *
     * @param string $mailAddress The email adres to validate
     *
     * @return bool
     * @since   1.0.3
     * @version 1.0
     *
     */
    public static function validateMail(string $mailAddress) : bool
    {
        if (strlen($mailAddress) > 254) {
            return false; // Mailadress is not allowed to be longer than 254 characters
        }

        $split  = explode("@", $mailAddress);
        $domain = array_pop($split); // domain
        $local  = implode("@", $split);

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
     * @param string $local The local part of the email address
     *
     * @return bool
     * @since       1.0.3
     * @version     1.0
     *
     */
    private static function validateLocal(string $local) : bool
    {

        if (strlen($local) >= 64) {
            return false; // local part is allowed to be up to 64 characters long
        }

        $quotedStringPart   = '`^\"([\\\a-zA-Z0-9\.@\(\)<>\[\]:,;\"!#$\%&\-/=?^_\'\`{}| ~]{1,})\"$`';
        $unquotedStringPart = '`^([a-zA-Z0-9\`\'\`\-!#$%&*+/=?^_{|}~]{1,}(\.{1}|)){1,}$`';

        if (preg_match($quotedStringPart, $local) > 0) {
            return true;
        } elseif (strpos($local, '."') > 0 || strpos($local, '".') > 0) {
            // ToDo rewrite part for quoted string part, as it does not check all quoted parts in the string
            $localParts = $local;
            if (strpos($local, '."') > 0) {
                $localParts = explode('."', $local);
                $preQuote   = $localParts[0];
                $localParts = '"'.$localParts[1];
            }

            if (strpos($local, '".') > 0) {
                $localParts = explode('".', $localParts);
                $postQuote  = $localParts[1];
                $localParts = $localParts[0].'"';
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
     * @param string $domain The domain of an email address
     *
     * @return bool
     * @since       1.0.3
     * @version     1.0.1
     *
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

            $domainRegex = "`(?:\[{1})([0-9.]{7,15})(?:\]{1})|(?:\[{1}IPv6\:)([0-9a-zA-Z:]{3,24})(?:\]{1})`";
            if (preg_match($domainRegex, $domain, $matches) > 0) {
                $match = $matches[2] ?? $matches[1] ?? null;
                if (is_null($match) === false) {
                    if (filter_var($match, FILTER_VALIDATE_IP) === false) {
                        return false;
                    }
                }

                return true;
            }
        } elseif (preg_match("`^[a-z0-9]{1}[a-z0-9\-.]*[a-z]{1}$`i", $domain) == 1) {
            return true;
        }

        return false;
    }


    /**
     * Determine if the input string can be a valid IBAN number
     *
     * @param string $iban The IBAN to check
     *
     * @return bool true if the IBAN appears to be valid
     * @since   1.0.6
     * @version 1.0.0
     *
     */
    public static function validateIBAN(string $iban) : bool
    {

        $checksum = (int)substr($iban, 2, 2);
        $landCode = substr($iban, 0, 2);
        $bankCode = substr($iban, 4);

        $landCode = implode(self::getIBANValueLetter(str_split($landCode, 1)));
        $bankCode = implode(self::getIBANValueLetter(str_split($bankCode, 1)));

        $fullNumber = $bankCode.$landCode.$checksum;

        return self::validateMod97($fullNumber);
    }

    /**
     * Perform a mod 97 on the input number
     *
     * @param string $number The number to validate, is a string to keep the number at its full length
     *
     * @return bool
     * @since   1.0.6
     * @version 1.0.0
     *
     */
    public static function validateMod97(string $number)
    {
        if (!is_numeric($number)) {
            return false;
        }

        $number = str_split($number, 9);
        if (count($number) > 1) {
            $number[0] = (int)$number[0] % 97;

            return self::validateMod97(implode($number));
        } else {
            return ((int)$number[0] % 97) == 1 ? true : false;
        }
    }

    /**
     * Convert a letter or array of letters to numbers, leaves numbers as they are
     *
     * @param $letter array|string letters to convert
     *
     * @return int|array
     * @since   1.0.6
     * @version 1.0.0
     *
     */
    private static function getIBANValueLetter($letter)
    {
        if (is_array($letter)) {
            foreach ($letter as &$value) {
                $value = self::getIBANValueLetter($value);
            }

            return $letter;
        }

        if (is_numeric($letter)) {
            return $letter;
        }

        return ord(strtolower($letter)) - ord("a") + 10;
    }

    /**
     * Checks the number against the Luhn mod10 algorithm
     *
     * @param string $number The number to be checked, is a string to keep the number at its full length
     *
     * @return bool True if the number is validates against the algorithm
     */
    public static function validateLuhnMod10(string $number)
    {
        if (!is_numeric($number)) {
            return false;
        }

        $parity = strlen($number) % 2;
        $total  = 0;
        $digits = str_split($number);
        foreach ($digits as $key => $digit) {
            if (($key % 2) == $parity) {
                $digit = ($digit * 2);
            }
            if ($digit >= 10) {
                $digit_parts = str_split($digit);
                $digit       = $digit_parts[0] + $digit_parts[1];
            }
            $total += $digit;
        }

        return $total % (10) == 0 ? true : false;
    }

    /**
     * Checks if the creditcard number could be a valid creditcardnumber
     *
     * @param string $number The number to be checked, is a string to keep the number at its full length
     *
     * @return bool
     */
    public static function validateCreditcardNumber(string $number)
    {
        if (strlen($number) > 19 || strlen($number) < 15) {
            return false;
        }

        return self::validateLuhnMod10($number);
    }

    /**
     * Check if the given array contains the given index. If it does not exist, set that index to the given value
     *
     * @param array      $array
     * @param string|int $index
     * @param string     $value
     */
    public static function setIfEmpty(array &$array, $index, $value = "")
    {
        if (isset($array[$index]) == false) {
            $array[$index] = $value;
        }
    }
}

