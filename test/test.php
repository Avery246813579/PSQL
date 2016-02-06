<?php

include_once('../src/Account.php');
include_once('../src/Database.php');
include_once('../src/Table.php');

$account = Account::withHost('127.0.0.1', 'root', '');
echo $account->toString() . '<br>';

$database = Database::withAccount($account, 'test');
echo $database->toString() . '<br>';

$table = Table::withDatabase($database, 'test4');
echo $table->toString() . '<br>';