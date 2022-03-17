<?php
session_start();


// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

unset($_SESSION['product_used_recipeName']);




    // フォーム未入力ならリダイレクト
    if (empty($_GET['id']) ) {
        
        header("Location: ./product_introduction.php?id=");
        // confirm.pnpへリダイレクト
        exit;
    }
     


    try {

            // テキスト入力フォームで検索した場合
            if (!empty($_GET['id'])) {
                // stmtで、文字列stringではなく、数値intだと（例えば＝191)と認識させるために、
                // 変数に代入する。
                $id = $_GET['id'];
               
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                // SELECT distinct で重複した行（レコード）をひとつにまとめる
                // ↑複数テーブル結合した場合は、WHERE句に対し、条件が完全一致するよう、
                // $_GET['id']の値を、全てのテーブルのカラムと連携させる必要がある。
                $stmt = $dbh->prepare("SELECT *
               
                 FROM product_lists
                INNER JOIN materials ON product_lists.id = materials.product_id
                INNER JOIN my_recipes ON materials.recipe_id = my_recipes.id
                WHERE product_lists.id LIKE $id 
                AND materials.product_id LIKE $id
                ");
                

                $stmt->execute();
             

                // 配列を変数に代入する
                $list = $stmt->fetchAll( PDO::FETCH_ASSOC );
          
            }  


                // if ($stmt->rowCount()) {
                if (!empty($list)) {

                    $_SESSION['product_used_recipeName'] = $list;
                    // 検索結果をセッションに渡す

                    // 未ログインなら
                    if(empty($_SESSION['member'])) {
                        header("Location: ./product_introduction_addlist_no_login.php?id=".$id);
                        exit;

                    } elseif (!empty($_SESSION['member'])) {
                        header("Location: ./product_introduction_addlist.php?id=".$id);
                        exit;
                    }
                

             
                } elseif (empty($list)) {
                   
                    $_SESSION['errMessage'] = '使われたレシピの登録はまだありません。';
                    header("Location: ./product_introduction_addlist.php?id=".$id);
                    // DB登録処理完了後、検索結果画面へ遷移する
                    exit;
                }        
           
            

    } catch (PDOException $e) {
            echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }



    ?>