<?php


namespace Jelmergu\DatabaseConnectors;
use Jelmergu\Exceptions\ConstantNotSetException;

class MySQL implements IDatabaseDSNConstructor
{
    /** @inheritdoc */
    public function getDSN() : string
    {
        $requiredFields = ['host', 'name'];

        foreach ($requiredFields as $field) {
            if (\defined('DB_'.strtoupper($field)) === false) {
                throw new ConstantNotSetException('DB_'.strtoupper($field));
            }
        }

        $extraFields = '';
        $options     = ['charset', 'port'];

        foreach ($options as $option) {
            if (\defined('DB_'.strtoupper($option)) === true) {
                $extraFields .= ';'.$option.'='.\constant('DB_'.strtoupper($option));
            }
        }

        return 'mysql:host='.DB_HOST.';dbname='.DB_NAME.$extraFields;
    }

}