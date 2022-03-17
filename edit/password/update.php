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
        // マイレシピ*テーブルのレコードをアップデートするsql文
        $sql = 'UPDATE members SET password=:password WHERE id=:id';

        // SQL文を実行する準備
        $stmt = $dbh->prepare($sql);

        // SQL（更新）の実行
        $stmt->bindValue(':password',sha1($_SESSION['members']['password']),PDO::PARAM_STR);
        $stmt->bindParam ( ":id", $_SESSION['members']['id'], PDO::PARAM_INT );

        // sqlを実行
        $stmt->execute ();


        // 処理が完了したら、あたらしいログインID（メールアドレス）で再ログインするために、（./login/join.php）へリダイレクト
        header("Location: ../../login/join.php?id=" . $_SESSION['id']);
        exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        exit;
    }
