<?php

class Table extends Database{
    public $table;

    public static function withDatabase($database, $table){
        $instance = new self();

        if($database instanceof Database){
            $instance->username = $database->username;
            $instance->password = $database->password;
            $instance->host = $database->host;
            $instance->database = $database->database;
        }else{
            //LOG
            die('ERROR');
        }

        $instance->table = $table;
        return $instance;
    }

    public function toString(){
        return parent::toString() . ' ' . $this->table;
    }
}