<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version 1.0
 * @since   1.0
 *
 * @package Jelmergu/Jelmergu
 */

namespace Jelmergu\Exceptions;
use Jelmergu\Date;

class FileNotFound extends Exception
{
    protected function setPrefix()
    {
        $this->prefix = "File not found";
    }
}