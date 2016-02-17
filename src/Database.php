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
            $instance->port = $account->port;
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
        if(isset($this->port)) {
            $connection = new mysqli($this->host, $this->username, $this->password, $this->database, $this->port);
        }else{
            $connection = new mysqli($this->host, $this->username, $this->password, $this->database);
        }

        if ($connection->connect_errno > 0) {
            die('DATABASE CONNECTION ERROR');
        }

        return $connection;
    }
}