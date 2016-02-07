<?php

include_once('../src/Account.php');
include_once('../src/Database.php');
include_once('../src/Table.php');
include_once('../src/Property.php');
include_once('../src/properties/Type.php');
include_once('../src/properties/Index.php');

$account = Account::withHost('127.0.0.1', 'root', '');
$database = Database::withAccount($account, 'test');
$table = Table::withDatabase($database, 'test3');
echo 'DROPPED: ' . $table->drop() . "<BR>";
$table->properties = [
    Property::withTL_AUTO_INDEX('ID', Type::INT, 10, Index::PRIMARY),
    Property::withTL('NAME',Type::VARCHAR, 25)
];
echo 'CREATED: ' . $table->create() . '<BR>';
echo 'ADDED: ' . $table->addRow(['ID' => 3, 'NAME' => 'Mexican']) . '<BR>';
echo 'GET ROW: ' . $table->getRow(['ID' => 3])['NAME'];

