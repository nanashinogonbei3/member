<?php

session_start();



require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');



// membersテーブルへ// インスタンス生成
$db_members = new Members();

try {

    // データベースに接続するための文字列（DSN 接続文字列）
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    // PDOクラスのインスタンスを作る
    // 引数は、上記のDSN、データベースのユーザー名、パスワード
    // XAMPPの場合はデフォルトでパスワードなし、MAMPの場合は「root」
    $dbh = new PDO($dsn, 'root', '');

    // エラーが起きたときのモードを指定する
    // 「PDO::ERRMODE_EXCEPTION」を指定すると、エラー発生時に例外がスローされる
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // membersテーブルのレコードをアップデートするsql文
    $sql = 'UPDATE members SET last_name=:last_name, first_name=:first_name, 
           nickname=:nickname, icon_img=:icon_img, phone_number=:phone_number WHERE id=:id';


    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);



    $stmt->bindParam(":last_name", $_SESSION['members']['last_name'], PDO::PARAM_STR);
    $stmt->bindParam(":first_name", $_SESSION['members']['first_name'], PDO::PARAM_STR);
    $stmt->bindParam(":nickname", $_SESSION['members']['nickname'], PDO::PARAM_STR);
    $stmt->bindParam(":icon_img", $_SESSION['members']['icon_img'], PDO::PARAM_STR);
    $stmt->bindParam(":phone_number", $_SESSION['members']['phone_number'], PDO::PARAM_STR);
    $stmt->bindParam(":id", $_SESSION['member'], PDO::PARAM_INT);

   
    $stmt->execute();


    // 処理が完了したら、マイページへリダイレクト
    header("Location: ../../login/process.php?id=" . $_SESSION['id']);
    exit;


} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    var_dump($e);
    echo $e->getMessage();
    exit;
}
