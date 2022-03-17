<?php


session_start();
// 必要なファイルを読み込む
require_once('../../create/recipe/class/db/Base.php');
require_once('../../create/recipe/class/db/CreateRecipes.php');

    // 調理手順・の画像のエラーチェックと画像一時保管先を指定します
    // 調理手順テーブル：proceduresのイメージ画像カラム名は、”p_img"
    $fileName = $_FILES['p_img']['name']; 


    if (!empty($fileName)) {
        
        $ext = substr($fileName, -3);
            if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
                $error['image'] = 'type';
                    echo '<p class= "error">* 写真などは「.gif」または「.jpg」
                    「.png」の画像を指定してください</p>' ;
                    exit;

                 }   

        } if (empty($error['image'])) {  

                        
            $image = date('YmdHis') .  $fileName;


            move_uploaded_file($_FILES['p_img']['tmp_name'],


            '../../create/recipe/pimg/' . $image);
            $_POST['p_img'] = $image;

          
        }  

try{


    // 調理手順＊テーブルへ// インスタンス生成
    $db_procedures = new Procedures(); 

            // 調理手順＊テーブルへのインサート実行
            $db_procedures->insert($_POST['p_recipe_id'], $_POST['descriptions'], $_POST['p_img']);
           

            // 処理が完了したら（confirm.php）へリダイレクト
            header("Location: ./confirm.php?id=" . $_POST['p_recipe_id']);
            // confirm.php からmy_recipe のid で飛ばされた、$_POST['recipe_id']
            exit;


    } catch (PDOException $e) {
        echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
        var_dump($e);
        echo $e->getMessage();
        exit;
    }
    

    ?>