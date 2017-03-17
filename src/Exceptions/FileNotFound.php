<?php
/**
 * @author  Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version
 *
 * @package Jelmergu
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