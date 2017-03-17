<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version v1.0.1
 *
 * @package Jelmergu
 */

namespace Jelmergu;


class Hashing
{

    /**
     * Validates the input password if it is md5 and converts hash to password_hash
     *
     * @version v1.0.1
     *
     * @param string $password The plain text to check the hash against
     * @param string $hash     The hash to check. Is passed as reference so will contain the resulting hash
     *
     * @return bool false if no conversion was made, true otherwise
     */
    public static function md5ToPassHash(string $password, string &$hash) : bool
    {

        if ($hash == md5($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Validates the input password and converts to password_hash
     *
     * @version v1.0.6
     *
     * @param string $password The plain text password to check against
     * @param string $hash     The hash to compare
     * @param string $salt     The optional salt passed to crypt. Will not be used with password_hash
     *
     * @return bool false if no conversion was made, true otherwise
     */
    public static function cryptToPassHash(string $password, string &$hash, string $salt = "")
    {
        if (crypt($password, $salt) == $hash) {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Check if the input hash was created using password_hash
     *
     * @note    Unknown if always correct
     *
     * @version v1.0.1
     *
     * @param string $hash The hash to check
     *
     * @return bool
     */
    public static function isPasswordHash(string $hash) : bool
    {
        return preg_match("`\\$2y\\$10\\$`", $hash) > 0;
    }
}