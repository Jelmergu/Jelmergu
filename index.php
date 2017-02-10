<?php
/**
 * @author      Jelmer Wijnja <info@jelmerwijnja.nl>
 * @version     0.1
 * @package     Jelmergu
 */

spl_autoload_register(
    function ($name) {
        $splitName = explode("\\", $name);
        if ($splitName[0] == "Jelmergu") {
            try {
                if (file_exists($name.".php")) {
                    include_once $name.".php";
                }
                else {
                    throw new Jelmergu\Exceptions\FileNotFound($name.".php");
                }
            }
            catch (Jelmergu\Exceptions\FileNotFound $e) {
                $e->toLog();
                die();
            }
            catch (Exception $e) {
                var_dump($e);
            }

        }
    }
);

