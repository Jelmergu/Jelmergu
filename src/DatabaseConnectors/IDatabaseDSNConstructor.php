<?php


namespace Jelmergu\DatabaseConnectors;


interface IDatabaseDSNConstructor
{
    /**
     * Returns the relevant Data Source Name(DSN) for the database
     *
     * @since
     * @throws \Jelmergu\Exceptions\ConstantNotSetException
     * @return string
     */
    public function getDSN() : string;
}