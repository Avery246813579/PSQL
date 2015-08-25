<?php

/**
 * Created by PhpStorm.
 * User: Avery
 * Date: 8/24/2015
 * Time: 8:24 PM
 */
include('../SqlHandler.php');
class Test extends Table
{
    public function __construct(){
        parent::__construct('localhost', 'root', '', 'theaverybot', 'Dogs');
        $this->variables = [
            'id' => 'INT NOT NULL AUTO_INCREMENT',
            'type' => 'VARCHAR(30)',
            'name' => 'VARCHAR(30)',
            'gender' => 'VARCHAR(30)',
            'age' => 'VARCHAR(30)'
        ];

        $this->primaries = array('id');
    }
}