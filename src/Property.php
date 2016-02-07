<?php

class Property
{
    public $name, $type, $length = 0, $auto_incrementing = false, $not_null = false, $default, $index;

    //TODO Check if const has right types
    public static function withTL($name, $type, $length){
        $instance = new self();
        $instance->name = $name;
        $instance->type = $type;
        $instance->length = $length;

        return $instance;
    }

    public static function withTL_AUTO($name, $type, $length){
        $instance = Property::withTL($name, $type, $length);
        $instance->auto_incrementing = true;

        return $instance;
    }

    public static function withTL_AUTO_INDEX($name, $type, $length, $index){
        $instance = Property::withTL_AUTO($name, $type, $length);
        $instance->index = $index;

        return $instance;
    }

    public static function withTLD($name, $type, $length, $default){
        $instance = Property::withTD($name, $type, $default);
        $instance->length = $length;

        return $instance;
    }

    public static function withTD($name, $type, $default){
        $instance = new self();
        $instance->name = $name;
        $instance->type = $type;
        $instance->default = $default;

        return $instance;
    }

    public function toString(){
        $query = $this->name . ' ' . $this->type;

        if($this->length > 0){
            $query .= '(' . $this->length . ')';
        }

        if($this->not_null){
            $query .= " NOT NULL";
        }

        if($this->auto_incrementing){
            $query .= " AUTO_INCREMENT";
        }

        if(isset($this->default)){
            $query .= " DEFAULT '" . $this->default . "'";
        }

        return $query;
    }
}