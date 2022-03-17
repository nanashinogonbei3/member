<?php 

session_start();
// このファイルは、会員情報を退会時に"完全削除"するためのプログラムです。
// 一時的に非表示にするには別ファイルを用意してあります。./update.php

require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');


try {
    // インスタンス生成
    $db = new Members();

   
    if (isset($_SESSION["del"]) ) {
        // レコードを削除する
        $db->delete($_SESSION['id']);
        // ログインidのセッションメンバーを削除する。
        unset($_SESSION['member']);
    } else {
        // class/db/CreateRecipe.php の
        // レコードをアップデートするSQL文
        // $sql = 'update materials set is_deleted=:isDeleted ';
        // レコードをアップデートする
        $db->updateIsCompletedByID($_SESSION['id'], $_SESSION['is_deleted']);
    }

    // /ecit/recipe/comfirm.phpへリダイレクトする
    // header("Location: ./edit/recipe/confirm.php?id=" . $_POST['id_select']);
    header("Location: ./process.php");
    exit;

} catch (Exception $e) {
    echo 'DBに接続できませんでした: ',  $e->getMessage(), "\n";
    var_dump($e);
    echo $e->getMessage();
    exit;
}


?>


