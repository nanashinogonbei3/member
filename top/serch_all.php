<?php
// このファイルでは、レシピ名と、レシピID、ニックネームを検索（あいまい検索）し、
// リレーションした調理手順の結果を渡す準備をします

session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');



        
        $errors=array();//ここで$errorsを初期化！

        if (empty($_GET)) {
            // フォーム未入力ならリダイレクト
            header("Location: ./confirm.php?id=".$_GET['id']);
            exit();

        } else {
            // エラーチェック
            if ($_GET['mushimegane'] ===" ")  { 
                $errors['mushimegane'] = 'blank';  
                $_SESSION['musimegane_blank'] = $errors['mushimegane'] = 'blank';
            } 
        }


    // もしエラーが無ければ、sql文の以下を実行する
    if (empty($error) ) {

        try {

                // 調理手順＊テーブルへ// インスタンス生成
                $my_recipes_db = new Myrecipes(); 

                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');

                //SQL文をPHPMyAdminの「sql」にコピペして動くか確認、; 曖昧% 検索するGETの値 %
                // SELECT * FROM product_lists WHERE product_name LIKE "%インド%" 


                // もしもレシピ名が入力されていたなら、
                if (!empty($_GET)) {  

                $sql = "SELECT my_recipes.id, my_recipes.recipe_name, my_recipes.complete_img, 
                my_recipes.cooking_time, my_recipes.cost, members.nickname, my_recipes.update_time
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id  AND is_released = 1 
                WHERE my_recipes.recipe_name LIKE '%" .$recipe_name. "%' OR nickname LIKE '%" .$nickname. "% "; 
                }    

                $stmt = $dbh->prepare($sql);

                // sqlを実行する
                $stmt->execute();
            
                // 全調理手順を表示するための、FETCHAll()
                $row= $stmt->fetchAll( PDO::FETCH_ASSOC );

                $_SESSION['serch'] = $row;

                foreach ($data as $v) {
                    echo $v['mushimegane'];
                }


                if (empty($data) === " " ) {
                    // エラーチェック
                    $errors['data_mushimegane'] = 'blank';  
                    $_SESSION['mushimegane_blank'] = $errors['data_mushimegane'];
                }


                header("Location: ./confirm.php?id=");
                // DB登録処理完了後、インデックスページ（index.php）へリダイレクト
                exit;



        } catch (PDOException $e) {
                echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                echo '<pre>';
                var_dump($e);
                echo '</pre>';
                // echo $e->getMessage();
                exit;
        }

             
    } 
    ?>