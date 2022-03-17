<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


// var_dump($_SESSION);
// exit;


// このファイルでは、レシピ名を検索（あいまい検索）し、リレーションした調理手順の結果を渡す準備をします

    // フォームのidが空だったらダイレクト
    if (empty($_SESSION['serch1']) ) {
        
        header("Location: ./login_recipe.php?id=");
        // confirm.pnpへリダイレクト
        exit;
   
    } else {
       
        foreach ($_SESSION['serch1'] as $v) {



        try {

            // 調理手順＊テーブルへ// インスタンス生成
            $my_recipes_db = new Myrecipes(); 

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn, 'root', '');

            //SQL文をPHPMyAdminの「sql」にコピペして動くか確認、; 曖昧% 検索するGETの値 %
            // SELECT * FROM product_lists WHERE product_name LIKE "%インド%" 

            
            if (!empty($_SESSION['serch1']['recipe_name'])) {
            
                    // レシピ名検索
                    $recipe_name = $_SESSION['serch1']['recipe_name'];

                        $sql = "SELECT my_recipes.id AS recipeid, my_recipes.recipe_name, my_recipes.complete_img,
                        my_recipes.cooking_time, my_recipes.cost, members.nickname, members.id
                        FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id 
                        WHERE CONCAT(recipe_name) LIKE '%$recipe_name%'
                        AND is_released = 1 ORDER BY my_recipes.created_date
                        ";


            } elseif (!empty($_SESSION['serch1']['nickname'])) {
                    // ニックネーム検索
                    $nickname = $_SESSION['serch1']['nickname'];

                        $sql = "SELECT my_recipes.id AS recipeid, my_recipes.recipe_name, my_recipes.complete_img,
                        my_recipes.cooking_time, my_recipes.cost, members.nickname, members.id
                        FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id 
                        WHERE CONCAT(nickname) LIKE '%$nickname%' 
                        AND is_released = 1 ORDER BY my_recipes.created_date
                        ";


            } elseif (!empty($_SESSION['serch1']['recipe_id'])) {
                    // レシピid検索
                    $recipeid = $_SESSION['serch1']['recipe_id'];

                        $sql = "SELECT my_recipes.id AS recipeid, my_recipes.recipe_name, my_recipes.complete_img,
                        my_recipes.cooking_time, my_recipes.cost, members.nickname, members.id
                        FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id                       
                        WHERE my_recipes.id LIKE $recipeid AND is_released = 1 ORDER BY my_recipes.created_date
                        ";


            } elseif (!empty($_SESSION['serch1']['cooking_time_a']) && !empty($_SESSION['serch1']['cooking_time_b'])) {
                    // クッキングタイム検索
                    $cooking_timeA = $_SESSION['serch1']['cooking_time_a'];
                    $cooking_timeB = $_SESSION['serch1']['cooking_time_b'];


                        $sql = "SELECT my_recipes.id AS recipeid, my_recipes.recipe_name, my_recipes.complete_img,
                        my_recipes.cooking_time, my_recipes.cost, members.nickname, members.id
                        FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id 
                        WHERE my_recipes.cooking_time BETWEEN $cooking_timeA AND $cooking_timeB
                        AND is_released = 1 ORDER BY my_recipes.cooking_time
                        ";
        
            } elseif (!empty($_SESSION['serch1']['cost_a']) && !empty($_SESSION['serch1']['cost_b'])) {
                    // コスト検索
                    $costA = $_SESSION['serch1']['cost_a'];
                    $costB = $_SESSION['serch1']['cost_b'];

                        $sql = "SELECT my_recipes.id AS recipeid, my_recipes.recipe_name, my_recipes.complete_img,
                        my_recipes.cooking_time, my_recipes.cost, members.nickname, members.id 
                        FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id 
                        WHERE my_recipes.cost BETWEEN $costA AND $costB
                        AND is_released = 1 ORDER BY my_recipes.cost
                        ";
            }  

                $stmt = $dbh->prepare($sql);

                // sqlを実行する
                $stmt->execute();
            
                // 全調理手順を表示するための、FETCHAll()
                $data= $stmt->fetchAll( PDO::FETCH_ASSOC );

                $_SESSION['search_recipe'] = $data;



        } catch (PDOException $e) {
                echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                echo '<pre>';
                var_dump($e);
                echo '</pre>';
                echo $e->getMessage();
                exit;
        }

                header("Location: ./login_recipe.php?id=");
                // DB登録処理完了後、インデックスページ（index.php）へリダイレクト
                exit; 
// end foreach
    } 
} 
 
    ?>