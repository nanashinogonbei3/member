<?php 



// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');


try {
    // インスタンス生成
    $db = new Procedures();


    // 削除チェックボックスにチェックが入っているとき
    // index3.php のinputform name='del'に☑があるとき削除し、sqlをアップロードする
    if (isset($_POST["del"])) {
        // レコードを削除する
        $db->delete($_POST['id']);
    } else {
        // class/db/CreateRecipe.php の
        // レコードをアップデートするSQL文
        // $sql = 'update materials set is_deleted=:isDeleted ';
        // レコードをアップデートする
        $db->updateIsCompletedByID($_POST['id'], $_POST['is_deleted']);
    }

    // /ecit/recipe/comfirm.phpへリダイレクトする
    // header("Location: ./edit/recipe/confirm.php?id=" . $_POST['id_select']);
    header("Location: ./confirm.php?id=" . $_POST['p_recipe_id']);
    exit;

} catch (Exception $e) {
    echo 'DBに接続できませんでした: ',  $e->getMessage(), "\n";
    var_dump($e);
    exit;
}


?>


