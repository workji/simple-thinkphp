<?php

$host = 'mysql'; // MySQLコンテナのサービス名
$dbname = 'test_db';
$username = 'root';
$password = 'root_pass';

# 新しいPDOオブジェクトを作成し、MySQLデータベースに接続
$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);

# SQL　文を実行
$stmt = $db->prepare('SELECT * FROM mytable');
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($results as $result) {
    echo $result['id'] . '. ' . $result['name'] . PHP_EOL;
}
