<?php
session_start();
// ログイン情報が無ければ、ログイン画面にリダイレクト
if (empty($_SESSION['member'])) {
    header('Location: ../login/join.php');
    exit();
}



// 必要なファイルを読み込む
require_once('../../class/db/Base.php');
require_once('../../class/db/CreateRecipes.php');

// 送信データを受け取る.(./login/process.php から遷移するときに受け取る、ログインメンバーのid)
$id = $_GET['id'];
// confirm.php からリダイレクトしてきたときに、受け取ってこのページの戻れるようにするための$_SESSION['id'];
$id = $_SESSION['id'];
// 成功 ./login/process.php から、<form action = "../edit/acount/modify."php?id=" <?php 、この"php?id=" で$_GET['id']を送っている。練習問題1-1


// ./member/login/process.php（マイページ画面からこの会員情報の変更画面に遷移したら、$_POST['id']を受け取ったことを条件に以下の処理を進める。
// ./member/edit/acount/confirm.phpからリダイレクト（書き直し）するために戻ってきたときに、$_SESSION['id']を受け取るときのid
// パラメータに$_GET、もしくは$_SESSION['id']が入っていたら処理を始める
if (!empty($_GET['id']) || !empty($_SESSION['id']) || !empty($_SESSION['member'])) {


    try {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // $id にはログイン中のメンバーIDが入っている、例えば68
        $sql = 'SELECT * FROM members WHERE id=' . $_SESSION['member'] . ' ';
        $sql .= 'ORDER BY created_date ASC';

        // // ・・・->prepare('SELECT email, username, firstname, middlename, lastname FROM members WHERE memberID = :memberID'); ←この部分に$sql 変数を代入する
        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchall(PDO::FETCH_ASSOC);

        foreach ($list as $v) {
            $last_name = $v['last_name'];
            $first_name = $v['first_name'];
        }
    } catch (Exception $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    }
} else {
    header('Location: ../../login/join.php');
    exit();
}


// フォームが送信した時だけエラーチェックを走らせる
if (!empty($_POST)) {
    // エラーチェック項目：
    if ($_POST['last_name'] === '') {
        $error['last_name'] = 'blank';
    }
    if ($_POST['first_name'] === '') {
        $error['first_name'] = 'blank';
    }

    if ($_POST['nickname'] === '') {
        $error['nickname'] = 'blank';
    }

    if ($_POST['phone_number'] === '') {
        $error['phone_number'] = 'blank';
    }



    $fileName = $_FILES['icon_img']['name'];
    // name 属性と$_FILES['name属性']にしなければいけない

    // 空→$fileName = $FILES['images']を、['icon_img']（ 【name="icon_img" 】と同じにしたら成功！）

    if (!empty($fileName)) {
        //「画像が空でなければ = アップロードされていれば」＝> 画像は必須項目ではないので、なくても検査を通過しても構いません。ですが、
        // 画像がアップロードしている場合で、「正しい画像ではない場合に、チェックを走らせます」

        // アップロードした画像ファイルが、「.jpg もしくは、.gif もしくは、.png 」かファイルの下３ケタを切り取って確認しよう
        // この、サブストラファンクションを使って、$fileName, -3は、ファイル名の「うしろ３文字の拡張子」を切り取るできるので、拡張子を検査できます
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
            $error['image'] = 'type';
            // （もしも、fileがjpg もしくはgif もしくは png(ピング)fileではなかったら、
            //  '画像タイプ'のエラーです
        }
    }

    // 上のエラー定義
    if (empty($error['image'])) {

        // 画像ファイル変数$imageをつくり、一時保存ファイル[name属性][tmp_name] に時刻ファンクションをくっつけて
        $image = date('YmdHis') .  $fileName;
        // というように2021112315167myface.png 別人が同じファイル名で上書き重複を防ぐため
        // YmdHisで、'登録時刻' . '['name属性']['tmp_nam']' で、データベースへ登録します  
        // ['tmp_name']は一時的に保存されます

        // echo $_FILES['image']; (☆ココで$_FILES['image']が空かどうかをチェックした)
        // exit;
        // 空原因：name属性とアンマッチ(正) :$_FILES['(name属性）icon_img']でfileに値が挿入できた！

        move_uploaded_file(
            $_FILES['icon_img']['tmp_name'],
            '../../member_picture/' . $image
        );
        // 入力された内容にエラーが無ければ、且つ画像データにエラーが無ければ、$_POSTの値を$_SESSION['members']に入れる
        // $_POSTに挿入されたファイルデータが、$_SESSION['members']に代入され、
        // $image（アップロードした画像を $_SESSION['members']['icon_img']に代入した
        $_SESSION['members'] = $_POST;
        $_SESSION['members']['icon_img'] = $image;
    }


    // 入力にエラーが無ければ、次の会員登録確認画面に遷移する
    if (empty($error)) {



        header('Location: confirm.php');
        exit();
    }
}


// 「確認画面（confirm.php）から「書き直す」ためにリダイレクトする
// [書き直しボタン]がリクエストされたら、再びPOSTフォームの編集画面が表示できる// ブラウザのヒストリ機能で戻ることもできるが、入力されたデータを正しく再現するために、
// かつ&&として、$_SESSION['members']が、正しく設定されている時だけ表示される（つまり編集が必要な場合だけ、という事）
if (!empty($_REQUEST['action']) && !empty($_SESSION['members'])) {
    // これを実現するには、フォームに、value="<?php print(htmlspecialchars($_SESSION['members']['nickname'],とセッションを書く
    if ($_REQUEST['action'] === 'rewrite' && isset($_SESSION['members'])) {
        $_POST = $_SESSION['members'];
    }
}

?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>会員情報の変更</title>

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="stylesheet2.css">
</head>


<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">会員情報の変更</p>
        </div>

        <div class="comprehensive">

            <!--  新規会員登録 -->
            <div class='inline_block_2'>


                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <div class="div_img3">
                            <dt>
                                <span style="font-size:16px;color:green;"><?php echo $last_name ?>
                                </span>様の会員登録の変更をします。「必須」の中は必ずご入力ください
                            </dt>
                        </div>
                        <p class="p_font_rarge"></p>
                        <div class="line"></div>
                    </div>


                    <!-- form action="" 。enctype ="multipart/form-data" は画像アップロードする時に必要 -->
                    <form action="" method="post" enctype="multipart/form-data">

                        <?php foreach ($list as $v) : ?>


                            <!-- 今ログイン中のID 'id' を隠して送る -->
                            <input type="hidden" name="id" <?php echo $_SESSION['member'] ?>>


                            <div class="div_img3">
                                <dt>●現在ご登録されている、お名前
                                    <span style="font-size:13px;color:green;"><?php echo $last_name ?></span>様
                                </dt>
                            </div>

                            <!-- 変更フォーム1 -->
                            <p class="wf-sawarabimincho">あたらしい会員名<span style="color:red">※必須</span></p>
                            <!-- maxlength= '入力できる制限文字数' -->
                            <?php if (!empty($_SESSION['members']['last_name'])) { ?>
                                <input type="text" name="last_name" size="35" placeholder='幸田弐' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                            $_SESSION['members']['last_name'],
                                                                                                                            ENT_QUOTES
                                                                                                                        )); ?>" />
                            <?php } else { ?>
                                <input type="text" name="last_name" size="35" placeholder='幸田弐' maxlength="255" value="" />
                            <?php } ?>

                            <!-- error -->
                            <?php if (!empty($error['last_name'])) : ?>
                                <p class="error">* お名前を入力してください</p>
                            <?php endif ?>

                            <!-- FETCH()された、現在のなまえ last nameカラム -->
                            <div class="div_img3">
                                <dt>●現在ご登録されている、お名前
                                    <span style="font-size:13px;color:green;"><?php echo $v['first_name']; ?></span>様
                                </dt>
                            </div>

                            <!-- 変更フォーム1 会員名のなまえの変更-->
                            <p class="wf-sawarabimincho">あたらしい会員名<span style="color:red">※必須</span></p>
                            <?php if (!empty($_SESSION['members']['first_name'])) { ?>
                                <!-- maxlength= '入力できる制限文字数' -->
                                <input type="text" name="first_name" size="35" placeholder='一郎' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                            $_SESSION['members']['first_name'],
                                                                                                                            ENT_QUOTES
                                                                                                                        )); ?>" />
                            <?php } else { ?>
                                <!-- maxlength= '入力できる制限文字数' -->
                                <input type="text" name="first_name" size="35" placeholder='一郎' maxlength="255" value="" />
                            <?php } ?>

                            <!-- error -->
                            <?php if (!empty($error['first_name'])) : ?>
                                <p class="error">* お名前を入力してください</p>
                            <?php endif ?>


                            <!-- FETCH()された、現在のニックネーム nicknameカラム -->
                            <div class="div_img3">
                                <dt>●現在ご登録されている、ニックネーム
                                    <span style="font-size:13px;color:green;"><?php echo $v['nickname']; ?></span>さん
                                </dt>
                            </div>
                            <!-- --------------------------------------------------------------- -->
                            <!-- 変更フォーム3 ニックネームの変更 -->
                            <p class="wf-sawarabimincho">あたらしいニックネーム<span style="color:red">※必須</span><br /></p>
                            <?php if (!empty($_SESSION['members']['nickname'])) { ?>
                                <input type="text" name="nickname" size="30" placeholder='くま吉' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                            $_SESSION['members']['nickname'],
                                                                                                                            ENT_QUOTES
                                                                                                                        )); ?>">
                            <?php } else { ?>
                                <input type="text" name="nickname" size="30" placeholder='くま吉' maxlength="255" value="">
                            <?php } ?>

                            <!-- error -->
                            <?php if (!empty($error['nickname'])) : ?>
                                <p class="error">* ニックネームを入力してください</p>
                            <?php endif ?>

                            <!-------------------------------------------------------------------- -->

                            <div class="div_img3">
                                <dt>●現在ご登録されている、アイコン画像</dt>
                                <img class="img3" src="../../member_picture/<?php echo $v['icon_img'] ?>" width="90px" height="auto">
                            </div>
                            <!-- 変更フォーム4 アイコン画像 -->
                            <p class="wf-sawarabimincho">あたらしいアイコン画像<span style="color:red">※必須</span><br /></p>
                            <input type="file" name="icon_img" maxlength="255" value="">

                            <!-- error msg -->
                            <!-- もしエラーがPOSTされた時に -->
                            <?php if (!empty($error['image'])) : ?>
                                <p class="error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
                            <?php endif ?>
                            <?php if (!empty($error)) : ?>
                                <!-- 確認画面からリライトして、もう一度ファイルを選びなおしてもらう時 -->
                                <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
                            <?php endif ?>
                            <!-- -------------------------------------------------------------------------- -->

                            <!-- FETCH()された、現在の電話番号 phone_numberカラム -->
                            <div class="div_img3">
                                <dt>●現在ご登録されている、お電話番号
                                    <span style="font-size:13px;color:green;"><?php echo $v['phone_number']; ?></span>
                                </dt>

                                <?php if (!empty($_SESSION['members']['phone_number'])) { ?>
                                    <!-- 変更フォーム5   お電話番号の変更 -->
                                    <p class="wf-sawarabimincho">あたらしいお電話番号(ハイフンなし）<span style="color:red">※必須</span></p>
                                    <input type="text" name="phone_number" size="30" placeholder='08012345678' maxlength="11" value="<?php print(htmlspecialchars(
                                                                                                                                            $_SESSION['members']['phone_number'],
                                                                                                                                            ENT_QUOTES
                                                                                                                                        )); ?>">
                                <?php } else { ?>
                                    <!-- 変更フォーム5   お電話番号の変更 -->
                                    <p class="wf-sawarabimincho">あたらしいお電話番号(ハイフンなし）<span style="color:red">※必須</span></p>
                                    <input type="text" name="phone_number" size="30" placeholder='08012345678' maxlength="11" value="">

                                <?php } ?>

                                <!-- error -->
                                <?php if (!empty($error['phone_number'])) : ?>
                                    <p class="error">* お電話番号を入力してください</p>
                                <?php endif ?>
                            </div>
                            <!-- ----------------------------------------------------------------------- -->
                            <div class="div_img3">
                                <!-- キャンセル ボタン -->
                                <dt class="wf-sawarabimincho">
                                    <input type="button" value='キャンセル' style="width: 115px; height: 25px" onclick="location.href='../../login/process.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">

                                    <!-- 確認ボタン -->

                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="submit" value="入力内容を確認する" />
                                </dt>
                            </div>

                        <?php endforeach ?>
                    </form>

                </div>

            </div>

        </div>

    </div>
    </div>

</body>

</html>