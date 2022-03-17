<?php

session_start();
// 必要なファイルを読み込む
require_once('./class/db/Base.php');
require_once('./class/db/CreateRecipes.php');

echo('<pre>');
var_dump($_SESSION['recipe']['how_many_servings']);
var_dump($_SESSION['recipe']['cost']);
echo('<pre>');
// exit;
// 成功!

   $fileName = $_FILES['video']['name']; 


   if (!empty($fileName)) {
        
        $ext = substr($fileName, -3);
                if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                        $error['image'] = 'type';
                }

        } if (empty($error['image'])) {


        $image = date('YmdHis') .  $fileName;


        move_uploaded_file($_FILES['video']['tmp_name'],


        './video/' . $image);
        // $_SESSION['recipe'] = $_POST;
        $_SESSION['recipe']['video'] = $image;
        

        }


try {

    // マイレシピ＊テーブルへ// インスタンス生成
    $my_recipes_db = new MyRecipes();
           
            // このインサート文と、add.php のインサート文の引数の数が合ってないとエラー：Fatal error: Uncaught ArgumentCountError: Too few arguments to function MyRecipes::insert(), 7 passed in
            // マイレシピ＊　テーブルへのインサート実行
            $my_recipes_db->insert($_SESSION['recipe']['recipe_name'], $_SESSION['recipe']['members_id'], 
            $_SESSION['recipe']['complete_img'], $_SESSION['recipe']['cooking_time'], $_SESSION['recipe']['cost'], $_SESSION['recipe']['how_many_servings'], 
            $_SESSION['recipe']['created_date'], $_SESSION['recipe']['video']);

            // レコードを全件取得する（期限日の古いものから並び替える）
            // $list = $db->selectAll();


            // DB登録処理完了後、マイレシピページ（index.php）へリダイレクト
            header("Location: ./index.php?id=" . $_SESSION['id']);
          
            exit; 


} catch (PDOException $e) {
        echo 'My_recipesのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        // echo $e->getMessage();
        exit;
}

?>