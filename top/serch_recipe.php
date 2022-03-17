<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');



// このファイルでは、レシピ名を検索（あいまい検索）し、リレーションした調理手順の結果を渡す準備をします

    // フォームのidが空だったらダイレクト
    if (empty($_SESSION['serch1']) ) {
        
        header("Location: ./login_recipe.php?id=");
        // confirm.pnpへリダイレクト
        exit;
   
    // これらの入力があれば、それぞれの変数に値を代入する
    } elseif (!empty($_SESSION['id']) || !empty($_SESSION['recipe_name']) || !empty($_SESSION['cooking_time_a']) 
        && !empty($_SESSION['cooking_time_b']) ||
        !empty($_SESSION['cost_a']) && !empty($_SESSION['cost_b']) || 
        !empty($_SESSION['recipe_id']) || !empty($_SESSION['nickname']) ) {

        // sql文のために、$_SESSION['serch']で受け取ったデータを変数に代入する。
        if (!empty($_SESSION['recipe_name'])) {
            $recipe_name = $_SESSION['recipe_name'];
        }
        if (!empty($_SESSION['cooking_time_a']) && !empty($_SESSION['cooking_time_b'])) {
            $cooking_timeA = $_SESSION['cooking_time_a'];
            $cooking_timeB = $_SESSION['cooking_time_b'];
        }
        if (!empty($_SESSION['cost_a']) && !empty($_SESSION['cost_b'])) {
            $costA = $_SESSION['cost_a'];
            $costB = $_SESSION['cost_b'];
        }
        if (!empty($_SESSION['recipe_id'])) {
            $recipe_id = $_SESSION['recipe_id'];
        }
        if (!empty($_SESSION['nickname'])) {
            $nickname = $_SESSION['nickname'];
        }
    



        try {

            // 調理手順＊テーブルへ// インスタンス生成
            $my_recipes_db = new Myrecipes(); 

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn, 'root', '');

            //SQL文をPHPMyAdminの「sql」にコピペして動くか確認、; 曖昧% 検索するGETの値 %
            // SELECT * FROM product_lists WHERE product_name LIKE "%インド%" 


                // もしもレシピ名が入力されていたなら、
                if (!empty($recipe_name)) {  

                $sql = "SELECT *
                FROM my_recipes JOIN members ON my_recipes.members_id = members.id WHERE recipe_name LIKE '%" .$recipe_name."%'
                AND is_released = 1 
                ORDER BY update_time
                ";
                }

              

                // もしもニックネームが入力されていたら、
                if (!empty($nickname) ) {

                $sql = "SELECT *
                FROM my_recipes JOIN members ON my_recipes.members_id = members.id WHERE nickname LIKE '%" .$nickname."%'
                AND is_released = 1 
                ORDER BY update_time
                ";
                }     
                
                // もしもニックネームとレシピ名が入力されていたら、
                if (!empty($nickname) && !empty($recipe_name) ) {

                $sql = "SELECT *
                FROM my_recipes JOIN members ON my_recipes.members_id = members.id WHERE nickname LIKE '%" .$nickname ."%' AND my_recipes.recipe_name 
                LIKE '%" .$recipe_name ."%' AND is_released = 1 ORDER BY update_time
                ";
                }   

                // もしもニックネームと調理時間が入力されていたら
                if (!empty($nickname) && !empty($cooking_timeA) && !empty($cooking_timeB)  )
                {

                $sql = "SELECT * 
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND members.nickname LIKE '%" .$nickname. "%' AND my_recipes.cooking_time 
                BETWEEN $cooking_timeA AND $cooking_timeB AND is_released = 1
                ORDER BY cooking_time
                ";
                }  
                
                // もしもニックネームが空で、調理時間が入力されていたら
                if (empty($nickname) AND !empty($cooking_timeA) && !empty($cooking_timeB) )
                {

                $sql = "SELECT * 
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND my_recipes.cooking_time BETWEEN $cooking_timeA AND $cooking_timeB AND is_released = 1
                ORDER BY cooking_time
                ";
                } 
                
                // もしもニックネームと材料費が入力されていたら
                if (!empty($nickname) AND !empty($costA) && !empty($costB) ) {

                $sql = "SELECT * 
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND nickname LIKE '%" .$nickname. "%' AND my_recipes.cost 
                BETWEEN $costA AND $costB AND is_released = 1
                ORDER BY cooking_time
                ";
                }   

                // もしもニックネームが空で、材料費が入力されていたら
                if (empty($nickname) AND !empty($costA) && !empty($costB) ) {

                $sql = "SELECT * 
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND my_recipes.cost BETWEEN $costA AND $costB
                ORDER BY cost
                ";
                }   
               
                // もしもレシピIDが入力されたら、   
                if (!empty($recipe_id) ) {
                        
                $sql = "SELECT *
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id  AND is_released = 1 
                AND my_recipes.id LIKE '" .$recipe_id. "' 
                ";
                }


                // もしもレシピ名と材料費 / もしくは、材料費が入力されたら、
                if (!empty($recipe_name) AND !empty($costA) && !empty($costB) 
                ) {

                $sql = "SELECT *
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND my_recipes.recipe_name LIKE '%" .$recipe_name. "%' AND my_recipes.cost BETWEEN $costA AND $costB
                ORDER BY cost
                ";
                }   

                // もしもレシピ名と調理時間が入力されたなら、
                if (!empty($recipe_name) && !empty($cooking_timeA) && !empty($cooking_timeB)  ) {

                $sql = "SELECT * 
                FROM my_recipes INNER JOIN members ON my_recipes.members_id = members.id   
                AND my_recipes.recipe_name LIKE '%" .$recipe_name. "%' AND my_recipes.cooking_time 
                BETWEEN $cooking_timeA AND $cooking_timeB ORDER BY cooking_time
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
} 
 
    ?>