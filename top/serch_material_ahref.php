<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');




    // フォーム未入力ならリダイレクト
    if (empty($_GET['material']) ) {
        
        header("Location: ../edit/recipe/release_recipe.php?id=");
        // リダイレクト
        exit;
    }
     


    try {

            // テキスト入力フォームで検索した場合
            if (!empty($_GET['material'])) {
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                $stmt = $dbh->prepare("SELECT my_recipes.id, my_recipes.recipe_name, my_recipes.complete_img,
                categories.categories_name, members.nickname, categories.categories_name,
                materials.material_name

                FROM materials
                

                JOIN my_recipes ON materials.recipe_id = my_recipes.id
                JOIN members ON my_recipes.members_id = members.id
                left outer JOIN recipe_categories ON my_recipes.id = recipe_categories.my_recipe_id
                left outer JOIN categories on categories.id = recipe_categories.category_id
                WHERE is_released = 1 AND my_recipes.is_deleted = 0
                AND material_name LIKE :material_name");
                $stmt->bindValue(":material_name", '%' . addcslashes($_GET['material'], '\_%') . '%');
               
                $stmt->execute();

                $list= $stmt->fetchAll( PDO::FETCH_ASSOC );




                if ($stmt->rowCount()) {

                    $_SESSION['materials1'] = $list;
                    // 検索結果をセッションに渡す

                    header("Location: ./acodion.php?id=");
                    // DB登録処理完了後、リダイレクト
                    exit;

             
                } elseif (empty($list)) {

                    if (isset($_SESSION['member'])) {         
                        $error3 = "検索結果がありません";
                        $_SESSION['error3'] = 
                        header("Location: ./confirm.php?id=");
                        // DB登録処理完了後、検索結果画面へ遷移する
                        exit;
                    
                    } else {

                        // 
                    }
                }      
           
            }

    } catch (PDOException $e) {
            echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
     
            exit;
    }


    ?>