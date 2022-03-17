<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


// このファイルでは、ユーザーのニックネームから検索（あいまい検索）し、membersテーブルから、メンバーの作ったレシピの検索
// 結果を渡す準備をします

    // フォーム未入力ならリダイレクト
    if (empty($_GET) ) {
        
        header("Location: ./acodion.php?id=");
        exit;
    }
     


    try {

           
            if (!empty($_GET['name'])) {
                
                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');


                // Qiita https://qiita.com/jyunia0110/items/7166c6146fbf0b9d8d80
                $stmt = $dbh->prepare("SELECT my_recipes.id, my_recipes.recipe_name,
                members.nickname,
                my_recipes.complete_img, categories.categories_name
                FROM members 
                JOIN my_recipes ON members.id = my_recipes.members_id
                JOIN recipe_categories ON my_recipes.id = recipe_categories.my_recipe_id
                JOIN categories ON recipe_categories.category_id = categories.id 
                WHERE nickname LIKE :nickname AND my_recipes.is_deleted = 0");
                $stmt->bindValue(":nickname", '%' . addcslashes($_GET['name'], '\_%') . '%');
                
                $stmt->execute();

                $user= $stmt->fetchAll( PDO::FETCH_ASSOC );


               foreach($user as $v){
           
                   $id = $v['id'];
               }
            
                // exit; addcslashes($_GET['name']のGETの名前にアンダーバー'_'をつけると不成功。アンダーバーを消したら出来た
                // 注意:

                if ($stmt->rowCount()) {

                    $_SESSION['members_recipes'] = $user;
                    // 検索結果をセッションに渡す

                    header("Location: ./acodion.php?id=" . $id);
                    // DB登録処理完了後、リダイレクト
                    exit;

             
                } else {
                    // エラー表示
                    if (isset($_SESSION['member'])) {
                        $error5 = 'お探しのユーザーがいません。'; 
                        $_SESSION['error5'] = $error5;
                        header("Location: ./confirm.php?id=");
                        
                        exit;
                    // エラー表示    
                    } else {
                        $error5 = 'お探しのユーザーがいません。'; 
                        $_SESSION['error5'] = $error5;
                        header("Location: ./index.php?id=");
                        
                        exit;

                    }
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


    ?>