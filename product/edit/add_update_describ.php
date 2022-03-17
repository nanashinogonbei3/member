<?php
session_start(); 

unset($_SESSION['errrMsg1']);
unset($_SESSION['describeLength']);


$id = $_POST['id'];

session_start();
// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

if(empty($_POST['describes']) ) {
        
        // （confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=".$id); 
        exit;
    }

// 制限値
$limit = 700;
// エラーメッセージ用変数の初期化
$errMsg1 = '';
// 入力された文字列の長さを取得する
$describesLength = strlen($_POST['describes']);
 
// 商品説明が、制限値を超えたらエラーを表示する。
if ($limit < $describesLength ) {
    $errMsg1 = "商品説明は、700文字以内で入力してください。";
    $_SESSION['errrMsg1'] = $errMsg1;

    $_SESSION['describeLength'] = $describesLength;
    // エラー内容を表示するためにconfirm.phpへリダイレクト
    header("Location: ./confirm.php?id=" . $_POST['id']);
    exit;
}
    try {

        $db_product_lists= new Product_lists(); 

        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

        $sql = "UPDATE product_lists SET describes=:describes 
        WHERE id=:id";

        // SQL文を実行する準備
        $stmt = $dbh->prepare ( $sql );

        $stmt->bindParam ( ":describes", $_POST['describes'], PDO::PARAM_STR);
        $stmt->bindParam ( ":id", $_POST['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();
                
        // 処理が完了したら（confirm.php）へリダイレクト
        header("Location: ./confirm.php?id=" . $_POST['id']);
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        // echo $e->getMessage();
        exit;
    }


?>