<?php
/**
 * @author    Jelmer Wijnja <info@jelmerwijnja.nl>
 * @copyright jelmerwijnja.nl
 * @version   1.0.4
 *
 * @package   Jelmergu
 */

namespace Jelmergu;


/**
 * A database trait containing shorthands for PDO
 *
 * This trait combines some methods of PDO that often get executed in sequence
 *
 * @package Jelmergu
 */
trait Database
{

    protected static $db;
    protected        $result;

    /**
     * Return a pdo instance
     *
     * @version 1.0.4
     *
     * @return PDO
     */
    protected function getPDO() : PDO
    {
        if (is_a(self::$db, "PDO") === FALSE) {
            self::$db = new PDO(
                "mysql:host=" . DB_HOST . ";
                dbname=" . DB_NAME . ";
                charset=". DB_CHARSET,
                DB_USERNAME,
                DB_PASSWORD
            );
        }

        return self::$db;
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
        $statement = $this->getPDO()->prepare($query);

        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        $statement->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetchAll(PDO::FETCH_ASSOC);
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
     * @return self
     */
    protected function queryData(string $query, $parameters = []) : self
    {
        $statement = $this->getPDO()->prepare($query);


        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        $statement->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetchAll(PDO::FETCH_ASSOC);

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
     * @return self
     */
    protected function queryRow($query, $parameters = []) : self
    {
        $statement = $this->getPDO()->prepare($query);

        foreach ($parameters as $parameter => $value) {
            if (strpos($parameter, ":") != 0) {
                $parameters[':' . $parameter] = $value;
                unset($parameters[$parameter]);
            }
        }

        $statement->execute($parameters);
        $this->result = $this->handleError($statement, $parameters)->fetch(PDO::FETCH_ASSOC);

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
            if ((bool) DEBUG === TRUE) {
                var_dump($this->fillQuery($query, $parameters));
                var_dump($statement->errorInfo());
            }
            else {
                if (file_exists(LOG_LOCATION . DIRECTORY_SEPARATOR . "database.log") === FALSE) {
                    if (is_dir(LOG_LOCATION) === FALSE) {
                        mkdir(LOG_LOCATION);
                    }
                }
                $file = fopen(LOG_LOCATION . DIRECTORY_SEPARATOR . "database.log", "a");
                fwrite($file, $statement->errorInfo()[2] . PHP_EOL . $this->fillQuery($query, $parameters));
                fclose($file);
            }
        }

        return $statement;
    }

    /**
     * Fill the prepared query with its parameters.
     *
     * @version 1.0.4
     *
     * @param $query
     * @param $parameters
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