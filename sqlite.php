<?php
// var_dump('sqlite:'.__DIR__.'/BD.sqlite');

// define("DB_DSN", 'sqlite:'.__DIR__.'/DB');
// $pdo = new PDO(DB_DSN);


$pdo = new PDO('sqlite:'.__DIR__.'/db.sqlite');
var_dump($pdo);

// $connection = new PDO('sqlite:'.__DIR__.'/BD.sqlite');
$pdo->exec(
    "INSERT INTO users (first_name) VALUES ('Manica')"
    );

print_r($pdo);