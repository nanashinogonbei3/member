<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');



// このファイルでは、テキストフォームで材料名を検索した結果を出力します

    // フォーム未入力ならリダイレクト
    if (empty($_GET['serch']) ) {

        // ログインしていたらconfirm.php へリダイレクト
        if (isset($_SESSION['member'])) {
        
            header("Location: ./confirm.php?id=");
            exit;

        // 未ログイン状態ならindex.php へリダイレクト
        } else {
            header("Location: ./index.php?id=");

            exit;
        }    


    } else {
     


    try {
            // テキスト入力フォームで検索した場合
      
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                // SELECT distinct で重複した行（レコード）をひとつにまとめる
                $stmt = $dbh->prepare("SELECT distinct
                    my_recipes.id, my_recipes.recipe_name, my_recipes.complete_img,
                    categories.categories_name, members.nickname
                   
                FROM
                my_recipes
                left outer JOIN members ON my_recipes.members_id = members.id
               
                left outer JOIN recipe_categories ON my_recipes.id = my_recipe_id
                left outer JOIN categories ON categories.id = recipe_categories.category_id
               
                WHERE
                my_recipes.is_released = 1
                AND my_recipes.is_deleted = 0
                AND (
                    recipe_name LIKE :serch 
                    OR nickname LIKE :serch  
                   
                    OR categories_name LIKE :serch
                    OR my_recipes.id = :serch2
                    ) 
                    " );


                $stmt->bindValue(":serch", '%' . addcslashes($_GET['serch'], '\_%') . '%');

                $stmt->bindValue(":serch2",  addcslashes($_GET['serch'], '\_%' ) );
                
                $stmt->execute();
         


                // 配列を変数に代入する
                $list = $stmt->fetchAll( PDO::FETCH_ASSOC );

                if ($stmt->rowCount()) {

                    $_SESSION['serch'] = $list;
                    // 検索結果をセッションに渡す
                    
                    header("Location: ./acodion.php?id=");
                    // DB登録処理完了後、検索結果画面へ遷移する
                    exit;
           

                } else {

                    if (isset($_SESSION['member'])) {
                        $error = "お探しのレシピの登録がありません。";
                        $_SESSION['error'] = $error;
                        header("Location: ./confirm.php?id=");
                        // エラー表示
                        exit;

                    } else {
                        $error = "お探しのレシピの登録がありません。";
                        $_SESSION['error'] = $error;
                        header("Location: ./index.php?id=");
                        // エラー表示
                        exit;
                    }
                } 

        
  
           

    } catch (PDOException $e) {
            echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
    }

    }
