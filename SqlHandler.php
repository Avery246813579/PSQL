<?php

/**
 * Created by PhpStorm.
 * User: Avery
 * Date: 8/24/2015
 * Time: 8:05 PM
 */
class Table
{
    public $hostname, $username, $password, $database, $table, $variables, $primaries;

    public function __construct($hostname, $username, $password, $database, $table)
    {
        $this->hostname = $hostname;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->table = $table;
    }

    public function createTable()
    {
        $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database) or die('Kittens');

        if ($connection->connect_errno > 0) {
            die('Unable to connect to database [' . $connection->connect_error . ']');
        }

        $table_content = "";
        if (is_array($this->variables)) {
            foreach ($this->variables as $key => $value) {
                $table_content = $table_content . ', ' . $key . ' ' . $value;
            }
        }

        $table_suffix = "";
        if (is_array($this->primaries)) {
            foreach ($this->primaries as $value) {
                $table_suffix = $table_suffix . ', PRIMARY KEY (' . $value . ')';
            }
        }

        $statement = $connection->prepare('CREATE TABLE IF NOT EXISTS ' . $this->table . ' (' . substr($table_content, 2, strlen($table_content)) . $table_suffix . ')');
        $statement->execute();
        $statement->close();

        $connection->close();
    }

    public function create($inputs)
    {
        $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database) or die('Kittens');

        if ($connection->connect_errno > 0) {
            die('Unable to connect to database [' . $connection->connect_error . ']');
        }

        $table_keys = "";
        $table_values = "";
        $prepared_keys = "";
        $prepared_values = array();
        if (is_array($inputs)) {
            foreach ($inputs as $key => $value) {
                $table_keys = $table_keys . ', ' . $key;
                $table_values = $table_values . ", ?";

                array_push($prepared_values, $value);

                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $statement = $connection->prepare('INSERT INTO ' . $this->table . ' (' . substr($table_keys, 2, strlen($table_keys)) . ') VALUES (' . substr($table_values, 2, strlen($table_values)) . ')');
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $statement->execute();
        $statement->close();

        $connection->close();
    }

    public function get($where)
    {
        $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database) or die('Kittens');

        if ($connection->connect_errno > 0) {
            die('Unable to connect to database [' . $connection->connect_error . ']');
        }

        $table_values = "";
        $prepared_keys = "";
        $prepared_values = array();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $table_values = $table_values . ', ' . $key . " = ?";

                array_push($prepared_values, $value);

                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $statement = $connection->prepare('SELECT * FROM ' . $this->table . ' WHERE ' . substr($table_values, 2, strlen($table_values)));
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $statement->execute();

        $result = $statement->get_result();
        $rows = array();
        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            array_push($rows, $row);
        }

        $statement->close();
        $connection->close();

        return $rows;
    }

    public function update($where, $values){
        $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database) or die('Kittens');

        if ($connection->connect_errno > 0) {
            die('Unable to connect to database [' . $connection->connect_error . ']');
        }

        $table_values = "";
        $prepared_keys = "";
        $prepared_values = array();
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $table_values = $table_values . ', ' . $key . " = ?";

                array_push($prepared_values, $value);

                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $table_where_values = "";
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $table_where_values = $table_where_values . ' AND ' . $key . " = ?";

                array_push($prepared_values, $value);

                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $statement = $connection->prepare('UPDATE ' . $this->table . ' SET ' . substr($table_values, 2, strlen($table_values)) . ' WHERE ' . substr($table_where_values, 4, strlen($table_where_values)));
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $result = $statement->execute();

        $statement->close();
        $connection->close();

        return $result;
    }

    public function delete($where){
        $connection = new mysqli($this->hostname, $this->username, $this->password, $this->database) or die('Kittens');

        if ($connection->connect_errno > 0) {
            die('Unable to connect to database [' . $connection->connect_error . ']');
        }

        $table_values = "";
        $prepared_keys = "";
        $prepared_values = array();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $table_values = $table_values . ' AND ' . $key . " = ?";

                array_push($prepared_values, $value);

                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $statement = $connection->prepare('DELETE FROM ' . $this->table . ' WHERE ' . substr($table_values, 4, strlen($table_values)));
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $result = $statement->execute();

        $statement->close();
        $connection->close();

        return $result;

    }

    function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
}