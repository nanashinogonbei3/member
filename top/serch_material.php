<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


if(!empty($_GET['material'])) {
    $materials = $_GET['material'];
    
}



if (!empty($materials)) {
   if(count($materials) ==1 ){
       echo '1個です。';
   } elseif(count($materials) >=2 ) {
       echo '2個以上デス。';
   }
}



    // フォーム未入力ならリダイレクト
    if (empty($_GET) ) {
        
        header("Location: ./acodion.php?id="); 
        exit;
    }
     

    // もし$_GETがあれば、sql文の以下を実行する
    if (!empty($materials) ) {


        try {


                $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                $dbh = new PDO($dsn, 'root', '');

            
            if (count($materials) == 1) {
                $a = implode($materials);
            } elseif(count($materials) >=2 ) {
                $a = implode("','",$materials);
            }
                // $c = implode("','",$materials);
                // $b = implode(",", $a);
                // implodeは、配列要素を連結する「ニンジン, 豆, じゃがいも」GETの配列をカンマ区切る
                $sql = "SELECT distinct
                    my_recipes.id, my_recipes.recipe_name, my_recipes.complete_img,
                    categories.categories_name, members.nickname, categories.categories_name,
                    materials.material_name
                FROM
                my_recipes
                left outer JOIN members ON my_recipes.members_id = members.id
                left outer JOIN materials ON my_recipes.id = materials.recipe_id
                left outer JOIN recipe_categories ON my_recipes.id = recipe_categories.my_recipe_id
                left outer JOIN categories on categories.id = recipe_categories.category_id
                WHERE is_released = 1 AND my_recipes.is_deleted = 0
                AND materials.material_name IN ('".$a."') ";

                $stmt = $dbh->prepare ( $sql );
                
                $stmt->execute();

                $result = $dbh->query ( $sql );

                $list = $result->fetchAll( PDO::FETCH_ASSOC );   
 
               

            // 検索したFETCHしたレシピIDとカテゴリーIDをセッションに渡し、
            // 表示画面でレシピの一覧を表示する 
            if(!empty($list)) {

                $_SESSION['materials1'] = $list;


                // DB登録処理完了後、リダイレクト
                header("Location: ./acodion.php?id=");
                
                exit;

            } elseif (empty($list)) {

                if (isset($_SESSION['member'])) {

                    // エラー表示
                    $error1 = 'この食材を使用したレシピの登録はありません。';
                    $_SESSION['error1'] = $error1;
                    header("Location: ./confirm.php?id=");
                    
                    exit; 
                
                } else {
                    // エラー表示
                    $error1 = 'この食材を使用したレシピの登録はありません。';
                    $_SESSION['error1'] = $error1;
                    header("Location: ./index.php?id=");
                    
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
    ?>