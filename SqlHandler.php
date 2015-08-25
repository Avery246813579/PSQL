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
        echo 'Test';

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

        $insert = 'CREATE TABLE IF NOT EXISTS ' . $this->table . ' (' . substr($table_content, 2, strlen($table_content)) . $table_suffix . ')';
        $statement = $connection->prepare($insert);
        echo $insert;
        $statement->execute();

        $connection->close();
    }
}