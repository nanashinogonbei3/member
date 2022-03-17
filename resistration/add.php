<?php

session_start();



try {
    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');
    
    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn,'root','');


    $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


    $sql='INSERT into members(last_name, first_name, members_id, nickname, icon_img, phone_number, password)';
    $sql.='values(:last_name, :first_name, :members_id, :nickname, :icon_img, :phone_number, :password)';


    $stmt = $dbh->prepare($sql);



    $stmt->bindValue(':last_name',$_SESSION['personal']['last_name'],PDO::PARAM_STR);
    $stmt->bindValue(':first_name',$_SESSION['personal']['first_name'],PDO::PARAM_STR);
    $stmt->bindValue(':members_id',$_SESSION['personal']['members_id'],PDO::PARAM_STR);
    $stmt->bindValue(':nickname',$_SESSION['personal']['nickname'],PDO::PARAM_STR);
    $stmt->bindValue(':icon_img',$_SESSION['personal']['icon_img'],PDO::PARAM_STR);
    $stmt->bindValue(':phone_number',$_SESSION['personal']['phone_number'],PDO::PARAM_INT);
    // sha1($_SESSION) sha1 でパスワードを暗号化することができます。
    $stmt->bindValue(':password',sha1($_SESSION['personal']['password']),PDO::PARAM_STR);
                            


    //SQLを実行します。
    $stmt->execute();



} catch (PDOException $e) {
  
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";

}


    header("Location: ./process.php");
    exit;

    // セッションを全て削除
    unset($_SESSION['personal']);

?>




