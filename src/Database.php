<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   1.0.4
 *
 * @package   Jelmergu
 */

namespace Jelmergu;

use PDO;
use PDOException;
use PDOStatement;

/**
 * A database trait containing shorthands for PDO
 *
 * This trait combines some methods of PDO that often get executed in sequence
 * Currently only supports MySQL
 *
 * @package Jelmergu
 */
trait Database
{

    /**
     * @var PDO $db Contains a single instance used by all instances of this class
     */
    protected static $db;
    /**
     * @var mixed $result Contains the results of the last database query
     */
    protected $result;
    /**
     * @var array $PDOOptions Contains options to be passed to the creation of the PDO instance
     */
    public static $PDOOptions = [];
    /**
     * @var array $DatabaseOptions Contains options for the Class
     *
     * @option Debug Determines the verbosity of the trait:
     *         0 = Silent, No errors are given
     *         1 = Exceptions, Only exceptions are thrown, default
     *         2 = Var Dumps, var_dumps are printed and exceptions are thrown
     * @option Log Determines what errors are send to log
     *         0 = Silent, No errors are logged
     *         1 = Exceptions, Only exceptions are thrown
     *         2 = Var Dumps, var_dumps are printed and exceptions are thrown, default
     *
     */
    public static $DatabaseOptions = ["Debug" => 1, "Log" => 2];

    public $fetchMethod = PDO::FETCH_ASSOC;

    /**
     * Return a pdo instance
     *
     * @version 1.0.4
     * @throws PDOException
     *
     * @return PDO
     */
    private function getPDO() : PDO
    {
        if (is_a(self::$db, "PDO") === FALSE) {
            $type = "mysql";
            if (defined("DB_TYPE") === TRUE) {
                $type = DB_TYPE;
            }
            if (defined("DB_HOST") === TRUE && defined("DB_NAME") === TRUE && defined("DB_USERNAME") === TRUE && defined("DB_PASSWORD") === TRUE) {
                $extraFields = "";
                $options = ["charset", "port"];

                foreach ($options as $option) {
                    if (defined("DB_" . strtoupper($option)) === TRUE) {
                        $extraFields .= ";" . $option . "=" . constant("DB_" . strtoupper($option));
                    }
                }

                self::$db = new PDO(
                    $type . ":host=" . DB_HOST . ";
                    dbname=" . DB_NAME
                    . $extraFields,
                    DB_USERNAME,
                    DB_PASSWORD,
                    self::$options
                );
            }
            else {
                $missingConstant = "";
                $requiredConstants = ["DB_HOST", "DB_NAME", "DB_USERNAME", "DB_PASSWORD"];
                foreach ($requiredConstants as $constant) {
                    if (defined($constant) === FALSE) {
                        $missingConstant .= $constant . ", ";
                    }
                }

                do {
                    if (self::$DatabaseOptions["Debug"] >= 1) {
                        throw new PDOException("Missing constants:" . $missingConstant);
                    }
                    elseif (self::$DatabaseOptions["Log"] >= 1) {
                        Log::DatabaseLog("Missing constants:" . $missingConstant);
                        continue;
                    }
                }
                while (FALSE);
            }
        }

        return self::$db;
    }

    /**
     * Prepare a query
     *
     * @param string $query The query to prepare
     *
     * @return PDOStatement
     */
    protected function prepare(string $query) : PDOStatement
    {
        return $this->getPDO()->prepare($query);
    }

    /**
     * Execute the query and count the affected rows
     *
     * @version 1.0.4
     *
     * @param int    $rows       The reference to the variable that will contain the amount of rows
     * @param string $query      The query to execute
     * @param array  $parameters The parameters for the query
     *
     * @return Database
     */
    protected function getRows(&$rows = 0, string $query, $parameters = []) : self
    {

        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        ($statement = $this->prepare($query))->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetchAll($this->fetchMethod);
        $this->fetchMethod = PDO::FETCH_ASSOC;
        $rows = $statement->rowCount();

        return $this;
    }

    /**
     * Execute a query and return all rows
     *
     * @version 1.0.4
     *
     * @param  string $query      The query to execute
     * @param array   $parameters The optional parameters for the prepared query
     *
     * @return Database
     */
    protected function queryData(string $query, $parameters = []) : self
    {

        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        ($statement = $this->prepare($query))->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetchAll($this->fetchMethod);
        $this->fetchMethod = PDO::FETCH_ASSOC;

        return $this;
    }


    /**
     * Execute a query and fetch a single
     *
     * @version 1.0.4
     *
     * @param  string $query      The query to execute
     * @param array   $parameters The optional parameters for the prepared query
     *
     * @return Database
     */
    protected function queryRow($query, $parameters = []) : self
    {

        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        ($statement = $this->prepare($query))->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetch($this->fetchMethod);
        $this->fetchMethod = PDO::FETCH_ASSOC;

        return $this;
    }

    /**
     * Handle for a sql error
     *
     * @version 1.0.4
     *
     * @param PDOStatement $statement  The statement with a possible error
     * @param array        $parameters The parameters used in the query
     *
     * @return PDOStatement
     */
    private function handleError(PDOStatement $statement, array $parameters) : PDOStatement
    {
        $query = $statement->queryString;
        if ($statement->errorCode() != "00000") {
            if (self::$DatabaseOptions["debug"] >= 2) {
                var_dump($this->fillQuery($query, $parameters));
                var_dump($statement->errorInfo());
            }
            elseif (self::$DatabaseOptions["debug"] >= 2) {
                Log::DatabaseLog($statement->errorInfo()[2] . PHP_EOL . $this->fillQuery($query, $parameters));
            }
        }

        return $statement;
    }

    /**
     * Fill the prepared query with its parameters.
     *
     * @version 1.0.4
     *
     * @param string $query      The query to fill
     * @param array  $parameters The parameters of the query
     *
     * @return string
     */
    function fillQuery(string $query, array $parameters) : string
    {
        foreach ($parameters as $key => $value) {
            $query = str_replace($key, '"' . $value . '"', $query);
        }

        return $query;
    }

}