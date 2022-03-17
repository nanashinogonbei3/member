<?php

session_start();

require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


// 送信データを受け取る
$_SESSION['members']['id'] = $_SESSION['id'];


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


    // string $update_time, string $release_date, int $is_deleted, int $id_select)
    // マイレシピ☆テーブルのレコードをアップデートするsql文
    $sql = 'UPDATE billing_addresses SET last_name=:last_name, first_name=:first_name, 
            phone_number=:phone_number, post_number=:post_number, 
            address1=:address1, address2=:address2, address3=:address3,
            address4=:address4, address5=:address5 WHERE id=:id';


    // SQL文を実行する準備
    $stmt = $dbh->prepare($sql);


    // SQL（更新）の実行
    $stmt->bindParam(":last_name", $_SESSION['address']['last_name'], PDO::PARAM_STR);
    $stmt->bindParam(":first_name", $_SESSION['address']['first_name'], PDO::PARAM_STR);
    $stmt->bindParam(":phone_number", $_SESSION['address']['phone_number'], PDO::PARAM_STR);
    $stmt->bindParam(":post_number", $_SESSION['address']['post_number'], PDO::PARAM_STR_CHAR);
    $stmt->bindParam(":address1", $_SESSION['address']['address1'], PDO::PARAM_STR);
    $stmt->bindParam(":address2", $_SESSION['address']['address2'], PDO::PARAM_STR);
    $stmt->bindParam(":address3", $_SESSION['address']['address3'], PDO::PARAM_STR);
    $stmt->bindParam(":address4", $_SESSION['address']['address4'], PDO::PARAM_STR);   
    $stmt->bindParam(":address5", $_SESSION['address']['address5'], PDO::PARAM_STR);
    $stmt->bindParam(":id", $_SESSION['address']['id'], PDO::PARAM_INT);

    // sqlを実行
    $stmt->execute();


    // 処理が完了したら、あたらしいログインID（メールアドレス）で再ログインするために、（./login/join.php）へリダイレクト
    header("Location: ./edit_address.php?id=" . $_SESSION['member']);
    exit; 


} catch (PDOException $e) {
    echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
    var_dump($e);
    echo $e->getMessage();
    exit;
}
