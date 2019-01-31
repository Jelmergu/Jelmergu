<?php
/**
 *
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @since     1.0.4
 * @version   2.0.0
 *
 * @package   Jelmergu/Jelmergu
 */

namespace Jelmergu;

use Jelmergu\Exceptions\PDOException;
use \PDO as PDO;
use \PDOStatement as PDOStatement;

class Database
{

    /**
     * @var array $PDOOptions Contains options to be passed to the creation of the PDO instance
     */
    public static $PDOOptions = [];
    /**
     * @var array $DatabaseOptions Contains options for the Class
     *
     * @option Debug Determines the verbosity of the trait:
     *         0 = Silent, No errors are given, default
     *         1 = Exceptions, Only exceptions are thrown
     *         2 = Var Dumps, var_dumps are printed and exceptions are thrown
     * @option Log Determines what errors are send to log
     *         0 = Silent, No errors are logged
     *         1 = Exceptions, Only exceptions are thrown
     *         2 = Var Dumps, var_dumps are printed and exceptions are thrown, default
     *
     */
    public static $DatabaseOptions = ["debug" => 0, "log" => 2];
    /**
     * @var int $fetchMethod The method used to fetch data
     * @see PDOStatement::fetch()
     */
    public static $fetchMethod = PDO::FETCH_ASSOC;
    /**
     * @var PDO $db Contains a single instance used by all instances of this class
     */
    protected static $db;
    /**
     * @var mixed $result Contains the results of the last database query
     */
    protected static $result;
    /**
     * @var bool $noTransactionErrors When a query in a transaction fails this will prevent the transaction from being committed
     */
    protected static $noTransactionErrors = true;

    private function __construct()
    {
    }

    public static function getSupportedTypes() : array
    {

        $supported = [
            "MySQL",
            "PostgreSQL",
        ];

        $drivers   = PDO::getAvailableDrivers();
        $available = [];

        foreach ($supported as $value) {
            if (in_array(strtolower($value), $drivers)) {
                $available[] = $value;
            }
        }

        return [];
    }

    /**
     * This method prepares the parameters for a prepared statement, executes the statement and handles some errors.
     * The method is intended to be used for statements that don't expect a result(DELETE, INSERT, UPDATE)
     *
     * @since   1.0.6
     * @version 2.0
     * @throws ConstantsNotSetException
     *
     * @param PDOStatement|string $statement       The statement to execute
     * @param array               $parameters      A list of key => value pairs, where some match the name of the parameters in
     *                                             the prepared statement. The keys don't need to be prefixed with a :
     * @param bool                $statementReturn Whether or not the statement should be returned for object chaining
     *
     * @return void|PDOStatement
     */
    public static function execute($statement, array $parameters = [], bool $statementReturn = false)
    {
        if (\is_string($statement)) {
            $statement = self::prepare($statement);
        }
        if (is_a($statement, PDOStatement::class)) {
            self::parametrize($statement->queryString, $parameters);
            $statement->execute($parameters);
            self::handleError($statement, $parameters);

            if ($statementReturn === true) {
                return $statement;
            }
        } else {
            self::handleException(new PDOException("\$Statement parameter given to \Jelmergu\Database was of incorrect type"), $statement, $parameters);

            return null;
        }
    }

    /**
     * This method executes multiple queries straight after each other
     *
     * @since   1.0.6
     * @version 2.0
     * @throws ConstantsNotSetException
     *
     * @param array $statements An array of statements
     * @param array $parameters An array of parameters. Can be more than needed for all queries
     *
     * @return void
     */
    public static function multiExecute(array &$statements, array $parameters = [])
    {
        foreach ($statements as &$statement) {
            $statement = self::execute($statement, $parameters, true);
        }
    }

    /**
     * Fill the prepared query with its parameters.
     *
     * @since   1.0.4
     * @version 2.0
     *
     *
     * @param string $query      The query to fill
     * @param array  $parameters The parameters of the query
     *
     * @return string
     */
    public static function fillQuery(string $query, array $parameters) : string
    {
        foreach ($parameters as $key => $value) {
            if ($key[0] != ':') {
                $key = ":{$key}";
            }
            $query = str_replace($key, "'".$value."'", $query);
        }

        return $query;
    }

    /**
     * Prepare a query
     *
     * @since   1.0.4
     * @version 2.0
     * @throws ConstantsNotSetException
     *
     * @param string $query The query to prepare
     *
     * @return PDOStatement
     */
    public static function prepare(string $query) : PDOStatement
    {
        return self::getPDO()
                   ->prepare($query);
    }

    /**
     * Parameterize every input parameter that is used by the query
     *
     * @since   1.0.6
     * @version 2.0
     *
     * @param string $query      The query to extract the parameters from
     * @param array  $parameters A list of parameters that might or might not be needed by the query
     *
     * @return Database
     */
    public static function parametrize(string $query, array &$parameters) : self
    {
        if (\count($parameters) > 0 && preg_match_all("`:([a-zA-Z0-9_]{1,})`", $query, $matches) !== false) {
            $outputParameters = [];
            foreach ($matches[1] as $key => $parameter) {
                if (isset($parameters[$matches[0][$key]]) === true) {
                    $outputParameters[":{$parameter}"] = $parameters[":{$parameter}"];
                    continue;
                } elseif (isset($parameters[$parameter]) === true) {
                    $outputParameters[':'.$parameter] = $parameters[$parameter];
                } else {
                    $e = new PDOException(
                        "Invalid parameter number: number of bound variables does not match number of tokens. Missing parameter '{$parameter}'",
                        'HY093');
                    self::handleException($e, $query, $parameters);
                }
            }
            $parameters = $outputParameters;
        }

        return new self();

    }

    /**
     * Prepare, execute and handle errors of the query and count the affected rows
     *
     * @since   1.0.4
     * @version 2.0
     *
     * @param int    $rows       The reference to the variable that will contain the amount of rows
     * @param string $query      The query to execute
     * @param array  $parameters The parameters for the query
     *
     * @return Database
     */
    public static function getRows(&$rows, string $query, array $parameters = []) : self
    {
        try {
            self::$fetchMethod = PDO::FETCH_ASSOC;
            self::$result      =
                ($statement =
                    self::execute(
                        $query,
                        $parameters,
                        true
                    )
                )->fetchAll(self::$fetchMethod);

            $rows = $statement->rowCount();
        } catch (\PDOException $e) {
            self::handleException($e, $query, $parameters);
        }

        return new self();
    }

    /**
     * Execute a query and return all rows
     *
     * @since   1.0.4
     * @version 2.0
     *
     * @param  string $query      The query to execute
     * @param array   $parameters The optional parameters for the prepared query
     *
     * @return Database
     */
    public static function queryData(string $query, array $parameters = []) : Database
    {
        try {
            self::$fetchMethod = PDO::FETCH_ASSOC;
            self::$result      = self::execute(
                $query,
                $parameters,
                true
            )->fetchAll(self::$fetchMethod);


        } catch (\PDOException $e) {
            self::handleException($e, $query, $parameters);
        }

        return new Database();
    }

    /**
     * Execute a query and fetch a single row
     *
     * @since   1.0.4
     * @version 2.0
     *
     * @param  string $query      The query to execute
     * @param array   $parameters The optional parameters for the prepared query
     *
     * @return Database
     */
    public static function queryRow($query, array $parameters = []) : self
    {
        try {
            self::$fetchMethod = PDO::FETCH_ASSOC;
            self::$result      = self::execute(
                $query,
                $parameters,
                true
            )->fetch(self::$fetchMethod);

        } // Handle the possible exception
        catch (\PDOException $e) {
            self::handleException($e, $query, $parameters);
        }

        return new self();
    }

    /**
     * This function returns whether or not a transaction is active
     *
     * @since   1.0.6
     * @version 2.0
     *
     * @throws ConstantsNotSetException
     * @return bool
     */
    public static function getTransaction() : bool
    {
        return self::getPDO()->inTransaction();
    }

    /**
     * Checks whether or not a transaction is without errors up to the point of calling.
     *
     * @since   2.0.0
     * @version 1.0
     *
     * @throws ConstantsNotSetException
     * @return bool True when a transaction is active and successfull up to the calling point, false when the transaction isn't set or has errored at some point
     */
    public static function transactionSucceeds() : bool
    {
        return self::getTransaction() && self::$noTransactionErrors;
    }

    /**
     * This function checks if a transaction is active. If there is, the transaction will be committed if it would succeed, rolled back otherwise. If not, a transaction is started
     *
     * @since   1.0.6
     * @version 2.0
     *
     * @throws ConstantsNotSetException
     * @return void
     */
    public static function transaction()
    {
        if (self::getTransaction() === false) {
            self::$noTransactionErrors = true;
            self::getPDO()->beginTransaction();

        } elseif (self::transactionSucceeds()) {
            self::getPDO()->commit();

        } else {
            self::getPDO()->rollBack();
        }
    }

    /**
     * Get the result of the last query
     *
     * @return mixed|void
     * @throws PDOException Throws a PDOException when there is no result in the query
     */
    public static function getResult()
    {
        if (self::$result === null) {
            throw new PDOException('No result from query');

            return;
        }

        return self::$result;
    }

    /**
     * Return a pdo instance
     *
     * @since   1.0.4
     * @version 2.0
     * @throws  ConstantsNotSetException
     *
     * @return PDO
     */
    private static function getPDO() : PDO
    {
        // Check if the PDO instance has been created
        if (is_a(self::$db, "PDO") === false) {
            if (!self::checkRequiredConstants(["DB_USERNAME", "DB_PASSWORD"])) {
                throw new ConstantsNotSetException();
            }

            self::$db = new PDO(
                self::prepareSettingsString(),
                DB_USERNAME,
                DB_PASSWORD,
                self::$PDOOptions
            );
        }

        return self::$db;
    }

    /**
     * Check if the required constants are set
     *
     * @since   2.0.0
     * @version 1.0
     *
     * @param array $requiredConstants
     *
     * @return bool
     */
    private static function checkRequiredConstants(array $requiredConstants) : bool
    {
        $missingConstant = [];

        foreach ($requiredConstants as $constant) {
            if (defined($constant) === false) {
                $missingConstant[] = $constant;
            }
        }
        if (count($missingConstant) !== 0) {
            $missing = "";
            foreach ($missingConstant as $constant) {
                $missing = empty($missing) === false ? "{$constant}" : "{$missing}, {$constant}";
            }
            self::handleException(new \PDOException("Missing constants: {$missing}"), "");

            return false;
        }

        return true;
    }

    /**
     * Prepare the settings string
     *
     * @since   2.0.0
     * @version 1.0
     *
     * @return string
     */
    private static function prepareSettingsString() : string
    {
        $type  = \defined("DB_TYPE") ? DB_TYPE : "MySQL";
        $class = 'DatabaseConnectors\\'.$type;

        return (new $class())->getDSN();
    }

    /**
     * Handle for a sql error
     *
     * @since   1.0.4
     * @version 2.0
     *
     * @param PDOStatement $statement  The statement with a possible error
     * @param array        $parameters The parameters used in the query
     *
     * @return PDOStatement
     */
    private static function handleError(PDOStatement $statement, array $parameters) : PDOStatement
    {
        $query = $statement->queryString;
        // Check if the statement was a success or not
        if ($statement->errorCode() != '00000') {
            self::$noTransactionErrors = false;
            // Make exception for file and linenumbers
            $e = new PDOException($statement->errorInfo()[2], $statement->errorCode());

            // Output to log
            if (self::$DatabaseOptions['log'] >= 2) {
                Log::databaseLog(
                    "{$e->getCode()}: {$e->getMessage()} in {$e->getFile()} at line {$e->getLine()}".
                    PHP_EOL.'Query: '.self::fillQuery($query, $parameters));
            }

            // Output to screen
            if (self::$DatabaseOptions['debug'] >= 2) {
                var_dump($e->getMessage());
                var_dump("{$e->getFile()} at {$e->getLine()}");
            }
        }

        return $statement;
    }

    /**
     * The handler for a Exceptions\PDOException
     *
     * @since   1.0.6
     * @version 1.0
     *
     *
     * @param \PDOException $e          The exception to handle
     * @param               $query      The query that causes the exception
     * @param array         $parameters The parameters of the query
     *
     * @return void
     */
    private static function handleException(\PDOException $e, $query, array $parameters)
    {
        /**
         * Check if the current exception is already the exception from this library
         * Note: somehow the exception created in Database::parametrize gets here as a \PDOException
         *  instead of a Exception\PDOException
         */
        if (get_parent_class($e) === 'RuntimeException') {
            $e = new Exceptions\PDOException($e->getMessage(), $e->getCode());
        }

        self::$noTransactionErrors = false;

        // Log the exception if allowed
        if (self::$DatabaseOptions['log'] >= 1) {
            Log::databaseLog(
                "{$e->getCode()}: {$e->getMessage()} in {$e->getFile()} at line {$e->getLine()}".
                PHP_EOL.'Query: '.self::fillQuery($query, $parameters)
            );
        }
        // Throw the exception if allowed
        if (self::$DatabaseOptions['debug'] >= 1) {
            throw new Exceptions\PDOException($e->getMessage(), $e->getCode());
        }
    }


}
