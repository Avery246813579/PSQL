<?php

class Database extends Account
{
    public $database;

    public static function withAccount($account, $database)
    {
        $instance = new self();

        if ($account instanceof Account) {
            $instance->host = $account->host;
            $instance->username = $account->username;
            $instance->password = $account->password;
        } else {
            //TODO LOG
            die('ERROR');
        }

        $instance->database = $database;
        return $instance;
    }

    public function toString()
    {
        return parent::toString() . ' ' . $this->database;
    }

    public function createConnection()
    {
        $connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        if ($connection->connect_errno > 0) {
            die('ERROR');
        }

        return $connection;
    }
}