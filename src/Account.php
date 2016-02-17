<?php

/**
 * Created by PhpStorm.
 * User: Avery
 * Date: 2/5/2016
 * Time: 10:00 AM
 */
class Account
{
    public $host, $port, $username, $password;

    public static function withHost($host, $username, $password){
        $instance = new self();
        $instance->host = $host;
        $instance->username = $username;
        $instance->password = $password;

        return $instance;
    }

    public static function withPort($host, $port, $username, $password){
        $instance = new self();
        $instance->host = $host;
        $instance->port = $port;
        $instance->username = $username;
        $instance->password = $password;

        return $instance;
    }

    public function toString(){
        return $this->host . " " . $this->port . " " .  $this->username . " " . $this->password;
    }
}