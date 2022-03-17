<?php

try{
 
$dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
$date = $dt->format('Y-m-d');

$dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

$dbh = new PDO($dsn,'root','');


$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


$sql = 'SELECT * FROM members ';
$sql .= 'ORDER BY created_date ASC';

//SQL文を実行する準備をします。
$stmt = $dbh->prepare($sql);

//SQLを実行します。
$stmt->execute();


} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
  exit;
}
?>