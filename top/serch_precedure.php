<?php
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


// このファイルでレシピ名を検索（あいまい検索）し、リレーションした調理手順の結果を渡す準備をします

    // フォーム未入力ならリダイレクト
    if (!isset($_SESSION['recipe_id']) ) {
        
        header("Location: ./login_recipe.php?id=");
        // confirm.pnpへリダイレクト
        exit;
   
    // これらの入力があれば、それぞれの変数に値を代入する
    } elseif (!empty ($_SESSION['recipe_id'])) {

        $myrecipe_id = $_SESSION['recipe_id'];

      
    } 

    


    // もしエラーが無ければ、sql文の以下を実行する
    if (empty($error)) {

        try {

            // // 調理手順＊テーブルへ// インスタンス生成
            // $my_recipes_db = new Myrecipes(); 

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn, 'root', '');


            if (!empty($myrecipe_id )) {

                // my_recipes, procedures, members ３つのテーブルをリレーション
                $sql = "SELECT my_recipes.id, my_recipes.recipe_name, procedures.p_recipe_id,
                procedures.descriptions, procedures.p_img, members.nickname, members.icon_img
                FROM my_recipes JOIN procedures ON my_recipes.id = procedures.p_recipe_id 
                AND is_released = 1 JOIN members ON my_recipes.members_id = members.id 
                WHERE my_recipes.id LIKE $myrecipe_id ORDER BY procedures.created_date";


                $stmt = $dbh->prepare($sql);

          

                // sqlを実行する
                $stmt->execute();
            
                // 全調理手順を表示するための、FETCHAll()
                $data= $stmt->fetchAll( PDO::FETCH_ASSOC );

                // var_dump($data);
                // exit;

                foreach ($data as $key => $v) {
                    // echo $v['recipe_name'];
                    // レシピ名
                    $_SESSION['recipe_id'] = $v['id'];
                    // レシピID
                    $_SESSION['recipename'] = $v['recipe_name'];
                    // 作った人
                    $_SESSION['nickname'] = $v['nickname'];
                    // 作った人のアイコン画像
                    $_SESSION['icon_img'] = $v['icon_img'];
                }
                
                $_SESSION['serchprecedures'] = $data;

            

            }

        } catch (PDOException $e) {
                echo 'ProceduresのDBに接続できません: ',  $e->getMessage(), "\n";
                echo '<pre>';
                var_dump($e);
                echo '</pre>';
                // echo $e->getMessage();
                exit;
        }

        header("Location: ./login_recipe.php?id=");
        // DB登録処理完了後、インデックスページ（index.php）へリダイレクト
        exit; 
    } 
    ?>