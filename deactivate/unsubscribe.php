<?php

session_start();

// 退会フォームからの$_POSTがない時あるいはいきなりこのページを開かれた場合はログイン画面に遷移する。
if (empty($_GET['id']) || empty($_SESSION['id'])) {

    header("Location: ../login/join.php");
    exit; 
}



// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

// ./member/login/process.php（マイページ画面からこの会員情報の変更画面に遷移したら、$_POST['id']を受け取ったことを条件に以下の処理を進める。
// ./member/edit/acount/confirm.phpからリダイレクト（書き直し）するために戻ってきたときに、$_SESSION['id']を受け取るときのid
if (!empty($_GET['id']) || !empty($_SESSION['id'])) {
    // 送信データを受け取る.(./login/process.php から遷移するときに受け取る、ログインメンバーのid)
    $id = $_GET['id'];
    // confirm.php からリダイレクトしてきたときに、受け取ってこのページの戻れるようにするための$_SESSION['id'];
    $id = $_SESSION['id'];





    try {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $id にはログイン中のメンバーIDが入っている、例えば68
        $sql = "SELECT * FROM members WHERE id= '" . $id . "' ";


        // ・・・->prepare('SELECT email, username, firstname, middlename, lastname FROM members WHERE memberID = :memberID'); ←この部分に$sql 変数を代入する
        $stmt = $dbh->prepare($sql);

        // $stmt->execute(array(':members_id' => $_SESSION['members_id']));
        $stmt->execute(array(':members_id' => $id));

        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);




    // 例外処理 
    } catch (Exception $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    }



} else {
    header("Location: ../login/join.php");
    exit; 
}



    if (!empty($_POST['del'])) {



        // メールアドレス
        if ($_POST['members_id'] === '') {
            $error['members_id'] = 'blank';
        }
        // パスワード
        if ($_POST['password'] === '') {
            $error['password'] = 'blank';
        }




        foreach ($list as $v) {
            // パスワード相違
            // = 'blank'の変数['password']と、'defarrence'の変数['password2']は上書きを防ぐために変える。
            if ($v['password'] !== sha1($_POST['password'])) {
                $error['password2'] = 'defarrence';
            }
        }

        // メール形式違反
        if (!filter_var($_POST['members_id'], FILTER_VALIDATE_EMAIL)) {
            $error['members_id2'] = 'valid email';
        }

        // 登録メールと一致しない
        $stmt = $dbh->prepare('SELECT members_id FROM members WHERE members_id = :members_id');

        $stmt->execute(array(':members_id' => $_POST['members_id']));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result == 0) {

            $error['email_member'] = 'defarrence';
        }



        // 入力にエラーが無ければ、$_POST['del'] の値をPOST
        if (empty($error)) {

            // action.phpへ$_POSTを渡すには、一度$_SESSIONに渡す必要があった。でないとNULLしか送れなかった
            $_SESSION['del'] =  $_POST;


            header("Location: ./action.php");
            exit;
        }
    }



?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>退会ページ</title>

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">



</head>



<body>

    <!-- Javascript ファイルを読み込む -->
    <script src="import.js"></script>
    <!-- 「退会」するボタンが押されたら、「本当に退会してよろしいですか？」とアラートが表示 -->
    <script src="./js/unsubscribe/unsubscribe.js"></script>


    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">退会</p>
            <div class="div_logout"><input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/process.php'">
                <!-- /member/logout/process.php -->
            </div>
        </div>

        <div class="comprehensive">

            <!--  会員の退会 -->
            <div class='inline_block_2'>


                <div class="inline_block_3">

                <?php foreach ($list as $v) : ?>



                    <div class="div_font_inline">


            
                        <!-- FETCH()された、現在のラストネーム ['last name']カラム -->
                        <div class="div_img3">
                            <dt>
                                <span style="font-size:15px;color:green;"><?php echo $v['last_name']; ?>&nbsp;</span>様の現在のメールアドレスとパスワードを入力し、「退会する」ボタンを押してください。
                            </dt>
                        </div>
                        <div class="line"></div>
                    </div>



                    <!-- フォーム -->
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <input type="hidden" name="is_deleted" value="1" <?php echo $v['is_deleted'] ?>>

                        <!-- フォーム1  会員ID / (メールアドレス) -->

                        <div class="div_img3">

                            <p class="wf-sawarabimincho">●現在の会員ID / メールアドレスを入力してください<span style="color:red">※必須</span></p>
                           
                            <!-- value="print(htmlspecialchars($_POST['members_id'], ENT_QUOTES)); " -->
                            <input type="text" name="members_id" size="35" placeholder='mail@gmail.com' maxlength="255" value="" />
                        </div>


                        <!-- 未入力 -->
                        <?php if (!empty($error['members_id'] = 'blank')) : ?>
                            <p class="error">*メールアドレスを入力してください</p>

                            <!-- 有効性 -->
                        <?php elseif ($error['members_id2'] === 'valid email') : ?>
                            <p class="error">*有効なメールアドレスを入力してください</p>

                            <!-- 相違 -->
                        <?php elseif ($error['email_member'] === 'defarrence') : ?>
                            <p class="error">* 入力されたメールアドレスが現在のメールアドレスと異なります</p>
                        <?php endif ?>


                        <!-- フォーム2  パスワード -->
                        <div class="div_img3">

                            <p class="wf-sawarabimincho">●現在のパスワードを入力してください<span style="color:red">※必須</span></p>
                            <!-- <span style="font-size:13px;color:green;">< $v['members_id']; ?></span> -->

                            <input type="text" name="password" size="35" placeholder='****' maxlength="255" value="" />
                        </div>

                     
                        <!-- 未入力 -->
                        <?php if (!empty($error['password'] = 'blank')) : ?>
                            <p class="error">*パスワードを入力してください</p>


                            <!-- 相違 -->
                        <?php elseif ($error['password2'] = 'defarrence') : ?>
                            <p class="error">* 入力されたパスワードが現在のパスワードと異なります</p>
                        <?php endif ?>


                        <div class="div_deactivate_after">

                            <div class="div_deactivate "></div>
                            <!-- 戻る ボタン  class="btn-border"-->
                            <dt class="wf-sawarabimincho">
                                <input type="button" value='マイページへ戻る' style="width: 135px; height: 35px" onclick="location.href='../login/process.php?id=<?php echo $_POST['id'] ?> action=rewrite'">
                        </div>

                        <div class="div_deactivate_del">
                            <!-- 退会ボタン -->
                            <!-- name ="del" $_POST['del'] -->
                            <input type="submit" name="del" value='退会する' class="unsubscribe_btn" onClick="return dispDelete();" style="
                                        font-size: 13px;
                                        width: 100px;
                                        height: 35px; 
                                        border-radius: 5px;
                                      
                                        color: #ffffff;
                                        background: #000000;">
                            </dt>



                        </div>

                <?php endforeach ?>

                </div>

                </form>



            </div>

        </div>
        <!-- div_Comprehensive -->
    </div>

    </div>
    </div>

</body>

</html>