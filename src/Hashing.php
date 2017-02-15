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
     * Check if the input hash is not a passwordHash and if it matches the md5 hash of input
     * Convert the input to password_hashed hash if both are true
     *
     * @version v1.0.1
     *
     * @param string $hash  The hash to check. Is passed as reference so will contain the resulting hash
     * @param string $input The plain text to check the hash against
     *
     * @return bool
     */
    public static function md5ToPassHash(string &$hash, string $input): bool
    {

        if (self::isPasswordHash($hash) === false) {
            if ($hash == md5($input)) {
                $hash = password_hash($input, PASSWORD_BCRYPT);

                return true;
            }
        }

        return false;
    }

    /**
     * Check if the input hash was created using password_hash
     *
     * @version v1.0.1
     *
     * @param string $hash The hash to check
     *
     * @return bool
     */
    public static function isPasswordHash(string $hash): bool
    {
        if (preg_match("`\\$2y\\$10\\$`", $hash) > 0) {
            return true;
        }

        return false;
    }
}