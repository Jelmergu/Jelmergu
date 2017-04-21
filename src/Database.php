<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   1.0.4
 *
 * @package   Jelmergu
 */

namespace Jelmergu;

use Jelmergu\Exceptions\PDOException as PDOException;
use PDO;
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
    public static $DatabaseOptions = ["debug" => 1, "log" => 2];

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
        if (is_a(self::$db, "PDO") === false) {
            $type = "mysql";
            if (defined("DB_TYPE") === true) {
                $type = DB_TYPE;
            }
            if (defined("DB_HOST") === true && defined("DB_NAME") === true && defined("DB_USERNAME") === true && defined("DB_PASSWORD") === true) {
                $extraFields = "";
                $options = ["charset", "port"];

                foreach ($options as $option) {
                    if (defined("DB_" . strtoupper($option)) === true) {
                        $extraFields .= ";" . $option . "=" . constant("DB_" . strtoupper($option));
                    }
                }

                self::$db = new PDO(
                    $type . ":host=" . DB_HOST . ";
                    dbname=" . DB_NAME
                    . $extraFields,
                    DB_USERNAME,
                    DB_PASSWORD,
                    self::$PDOOptions
                );
            } else {
                $missingConstant = "";
                $requiredConstants = ["DB_HOST", "DB_NAME", "DB_USERNAME", "DB_PASSWORD"];
                foreach ($requiredConstants as $constant) {
                    if (defined($constant) === false) {
                        $missingConstant .= $constant . ", ";
                    }
                }

                do {
                    if (self::$DatabaseOptions["debug"] >= 1) {
                        throw new \PDOException("Missing constants:" . $missingConstant);
                    } elseif (self::$DatabaseOptions["log"] >= 1) {
                        Log::DatabaseLog("Missing constants:" . $missingConstant);
                        continue;
                    }
                } while (false);
            }
        }

        return self::$db;
    }

    /**
     * Prepare a query
     *
     * @version 1.0.4
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
     * This method prepares the parameters for a prepared statement, executes the statement and handles some errors
     *
     * @version 1.0.6
     *
     * @param PDOStatement $statement  The statement to execute
     * @param array        $parameters A list of key => value pairs, where some match the name of the parameters in
     *                                 the prepared statement. The keys don't need to be prefixed with a :
     *
     * @return void|PDOStatement
     */
    public function execute(PDOStatement &$statement, array $parameters = [], $statementReturn = false)
    {
        $this->parametrize($statement->queryString, $parameters);
        $statement->execute($parameters);
        $this->handleError($statement, $parameters);

        if ($statementReturn === true) {
            return $statement;
        }
    }

    /**
     * Parameterize every input parameter that is used by the query
     *
     * @version 1.0.6
     * @throws \Jelmergu\Exceptions\PDOException
     *
     * @param string $query      The query to extract the parameters from
     * @param array  $parameters A list of parameters that might or might not be needed by the query
     *
     * @return Database
     */
    protected function parametrize(string $query, array &$parameters) : self
    {
        if (count($parameters) > 0 && preg_match_all("`:([a-zA-Z0-9_]{1,})`", $query, $matches) !== false) {
            foreach ($matches[1] as $key => $parameter) {
                if (isset($parameters[$matches[0][$key]]) === true) {
                    continue;
                } elseif (isset($parameters[$parameter]) === true) {
                    $outputParameters[':' . $parameter] = $parameters[$parameter];
                } else {
                    throw new PDOException("Invalid parameter number: number of bound variables does not match number of tokens. Missing parameter '{$parameter}'",
                        "HY093");
                }
            }
            $parameters = $outputParameters;
        }

        return $this;

    }

    /**
     * Prepare, execute and handle errors of the query and count the affected rows
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
        try {
            $this->result = $this->execute(
                $statement = $this->prepare($query),
                $parameters,
                true
            )->fetchAll($this->fetchMethod);
            $this->fetchMethod = PDO::FETCH_ASSOC;
            $rows = $statement->rowCount();
        } // Convert PHP's PDOException to the more accurate Jelmergu\PDOException
        catch (\PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }

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
        try {
            $this->result = $this->execute(
                $statement = $this->prepare($query),
                $parameters,
                true
            )->fetchAll($this->fetchMethod);
            $this->fetchMethod = PDO::FETCH_ASSOC;

        } // Convert PHP's PDOException to the more accurate Jelmergu\PDOException
        catch (\PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }

        return $this;
    }


    /**
     * Execute a query and fetch a single row
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
        try {
            $this->result = $this->execute(
                $statement = $this->prepare($query),
                $parameters,
                true
            )->fetch($this->fetchMethod);
            $this->fetchMethod = PDO::FETCH_ASSOC;

        } // Convert PHP's PDOException to the more accurate Jelmergu\PDOException
        catch (\PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }

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
            } elseif (self::$DatabaseOptions["debug"] >= 2) {
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
    public function fillQuery(string $query, array $parameters) : string
    {
        foreach ($parameters as $key => $value) {
            $query = str_replace($key, '"' . $value . '"', $query);
        }

        return $query;
    }

    /**
     * This function returns whether or not a transaction is active
     *
     * @version 1.0.6
     *
     * @return bool
     */
    protected function getTransaction() : bool
    {
        return $this->getPDO()->inTransaction();
    }

    /**
     * This function switches auto commit
     *
     * @version 1.0.6
     *
     * @return void
     */
    protected function transaction()
    {
        if ($this->getTransaction() === false) {
            $this->getPDO()->beginTransaction();
        } else {
            $this->getPDO()->commit();
        }
    }
}