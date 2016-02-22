<?php

include_once('properties/Filter.php');
include_once('properties/Index.php');

class Table extends Database
{
    /**
     * TODO:
     * - Check if table is created
     * - Check if add/get element is in table (throws mysqli_stmt_bind_param)
     * - Check AUTO INCREMENTING has key
     */
    public $name, $properties = [];

    public static function withDatabase($database, $table)
    {
        $instance = new self();

        if ($database instanceof Database) {
            $instance->username = $database->username;
            $instance->password = $database->password;
            $instance->host = $database->host;
            $instance->port = $database->port;
            $instance->database = $database->database;
        } else {
            //TODO LOG
            die('ERROR');
        }

        $instance->name = $table;
        return $instance;
    }

    public function addProperty($property)
    {
        array_push($this->properties, $property);
    }

    public function create()
    {
        $properties = "";
        $primary = array();
        $foreign = array();

        if (is_array($this->properties)) {
            foreach ($this->properties as $value) {
                if ($value instanceof Property) {
                    $properties .= ', ' . $value->toString();

                    if($value->index == Index::PRIMARY){
                        array_push($primary, $value->name);
                    }

                    if($value->isForeignSet()){
                        array_push($foreign, $value);
                    }
                }
            }
        } else {
            //TODO LOG
            die('CREATE ERROR');
        }

        $connection = $this->createConnection();
        $query = "CREATE TABLE " . $this->name . '(' . substr($properties, 2);

        if(count($primary) > 0){
            $query .= ', PRIMARY KEY (';

            $names = '';
            foreach($primary as $value){
                $names .= ', ' . $value;
            }

            $query .= substr($names, 2) . ')';
        }

        if(count($foreign) > 0){
            $delta = '';

            foreach($foreign as $value){
                if($value instanceof Property){
                    $delta .= ', FOREIGN KEY (' . $value->name . ') REFERENCES ' . $value->getForeign_Table() . '(' . $value->getForeign_Key() . ')';
                }
            }

            $query .= $delta;
        }

        $query .= ')';

        $result = $connection->query($query);
        return $result;
    }

    public function addRow($inputs)
    {
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
        } else {
            //TODO Add Log
            die("ADD ROW ERROR");
        }

        $connection = $this->createConnection();
        $statement = $connection->prepare('INSERT INTO ' . $this->name . ' (' . substr($table_keys, 2) . ') VALUES (' . substr($table_values, 2) . ')');
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $status = $statement->execute();
        $statement->close();
        $connection->close();

        return $status;
    }

    public function getRow($where)
    {
        return $this->getRowsWith($where, Filter::_AND)[0];
    }

    public function getRowWith($where, $filter){
        return $this->getRowsWith($where, Filter::_AND)[0];
    }

    public function getRows($where)
    {
        return $this->getRowsWith($where, Filter::_AND);
    }

    public function getRowsWith($where, $filter)
    {
        if ($filter != Filter::_OR && $filter != Filter::_AND) {
            die('FILTER ERROR GET WITH');
            //TODO LOG
        }

        $table_values = "";
        $prepared_keys = "";
        $prepared_values = array();
        if (is_array($where)) {
            foreach ($where as $key => $value) {
                $table_values = $table_values . ' ' . $filter . ' ' . $key . " = ?";
                array_push($prepared_values, $value);
                if (is_numeric($value)) {
                    $prepared_keys = $prepared_keys . 'i';
                } else {
                    $prepared_keys = $prepared_keys . 's';
                }
            }
        }

        $connection = $this->createConnection();
        $statement = $connection->prepare('SELECT * FROM ' . $this->name . ' WHERE ' . substr($table_values, 4));
        call_user_func_array('mysqli_stmt_bind_param', array_merge(array($statement, $prepared_keys), $this->refValues($prepared_values)));
        $statement->execute();
        $row = array();
        $this->stmt_bind_assoc($statement, $row);
        $rows = array();

        while ($statement->fetch()) {
            array_push($rows, $row);
        }

        $statement->close();
        $connection->close();
        return $rows;
    }


    /**
     * Drops a table
     *
     * @return bool|mysqli_result   Result of query
     */
    public function drop()
    {
        return parent::createConnection()->query('DROP TABLE ' . $this->name);
    }

    public function toString()
    {
        return parent::toString() . ' ' . $this->name;
    }

    private function stmt_bind_assoc(&$stmt, &$out)
    {
        $data = mysqli_stmt_result_metadata($stmt);
        $fields = array();
        $out = array();
        $fields[0] = $stmt;
        $count = 1;
        while ($field = mysqli_fetch_field($data)) {
            $fields[$count] = &$out[$field->name];
            $count++;
        }
        call_user_func_array('mysqli_stmt_bind_result', $fields);
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