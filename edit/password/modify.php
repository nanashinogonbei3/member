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

// 送信データを受け取る.(./login/process.php から「パスワードを変更する」で遷移した時に受け取る、ログインメンバーのid)
$id = $_GET['id'];
// confirm.php から「パスワードの変更」の「書き直し」でリダイレクトしてきたときに、受け取ってこのページの戻れるようにするための$_SESSION['id'];
$id = $_SESSION['id'];


// ./member/login/process.php（マイページ画面からこの会員情報の変更画面に遷移したら、$_POST['id']を受け取ったことを条件に以下の処理を進める。
// ./member/edit/password/confirm.phpからリダイレクト（書き直し）するために戻ってきたときに、$_SESSION['id']を受け取るときのid
if (!empty($_GET['id']) || !empty($_SESSION['id']) || !empty($_SESSION['member'])) {

    try {

        $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
        $date = $dt->format('Y-m-d');


        $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

        $dbh = new PDO($dsn, 'root', '');

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // カラムを「パスワード」を表示、「今ログイン中のひとの一人のパスワードなのでこれだけでオーケー！
        // DB接続成功 $idは、冒頭で送信データを受け取った、$id = $_GET['id']; でつくった変数idをDBのidとイコールでひもづけています
        $sql = "SELECT password FROM members WHERE id= '" . $_SESSION['member'] . "' ";

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchall(PDO::FETCH_ASSOC);




        /// 例外が発生したときの処理 ///
    } catch (Exception $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    }
} else {
    header('Location: ../../login/join.php');
    exit();
}

// フォームが送信した時だけエラーチェックを走らせる
if (isset($_POST['send'])) {
    // エラーチェック項目：  
    //-----------------------------------   
    // pw1
    if (empty($_POST['password'])) {
        $error['password'] = 'blank';
    }
    // strlen は、入力文字数の数を返してくれます
    // pwが4ケタ以下ならエラー。
    if (strlen($_POST['password']) < 4) {
        $error['password_length'] = 'length';
    }
    // ----------------------------------
    // pw2 
    if (empty($_POST['password2'])) {
        $error['password2'] = 'blank2';
    }
    // 確認用パスワード・フォーム
    // pwが4ケタ以下ならエラー。
    if (strlen($_POST['password2']) < 4) {
        $error['password2_length'] = 'length2';
    }

    // -----------------------------------
    // もし「確認用パスワード・フォーム」のパスワード['password2']と
    // 1番目に入力したパスワード['password']が相違していたらエラー
    if ($_POST['password2'] !== $_POST['password']) {
        $error['password2_difference'] = 'difference';
    }

    // 入力されたパスワードが、DB登録されたパスワードと同じパスワードがPOSTされた時はエラーを表示する
    // if (isset($_POST)) {    

    foreach ($list as $v) {

        // 実行結果 / 91b92669ecf0fa9c6e550fd5fd76c31b5c969f57
        if ($v['password'] === sha1($_POST['password'])) {
            $error['password_duplicate'];
            // $v['password']=’現在DB登録のパスワード 右が今フォームに入力されたパスワードが完全一致したら、エラー
            // パスワードが変わっていない（現在登録のDBのパスワードと全く同じパスワードの為エラー）
        }
        //  sha1($_POST['password'])
        // 今入力される生のパスワードと、DBの暗号化されたパスワード(sha1 不可逆型パスワード元に戻せない強力な暗号化
        // 必ず暗号化された文字列になるという特性を持ちます。だから今入力される生のPWの文字もsh1で暗号化してDBの文字列と照合します
    }

    // 入力にエラーが無ければ、次のPW変更確認画面に遷移する
    if (empty($error)) {

        $_SESSION['members'] = $_POST;
        header('Location: ./confirm.php');
        exit();
    }
}


?>
<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>会員登録</title>

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">
</head>


<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">パスワード変更</p>
        </div>

        <div class="comprehensive">


            <div class='inline_block_2'>



                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">パスワードの変更を行います</p>
                        <div class="line"></div>
                    </div>


                    <!-- フォーム -->

                    <form action="" method="post">

                        <!-- 冒頭で代入した $id = $_GET['id'] (今ログイン中のID 'id' をhidden送る -->
                        <input type="hidden" name="id" <?php echo $id ?>>

                        <!-- フォーム6 パスワード -->
                        <p class="wf-sawarabimincho">パスワード<span style="color:red">※必須</span></p>
                        <input type="password" name="password" placeholder='・・・・・・' maxlength="255" value="">


                        <!-- error -->
                        <!-- もしPOSTされた時に -->
                        <?php if (!empty($error['password'])) : ?>
                            <p class="error">* パスワードを入力してください</p>
                        <?php endif ?>

                        <?php if (!empty($_POST['password'])) { ?>
                            <?php if (!empty($error['password_length'])) : ?>
                                <p class="error">* パスワードを4桁以上にしてください</p>
                            <?php endif ?>
                            <!-- もし今と同じパスワードが入力されたら -->
                            <?php if (!empty($error['password_duplicate'])) : ?>
                                <p class="error">* 今と違うパスワードを入力してください</p>
                            <?php endif ?>
                        <?php } ?>

                        <!-- フォーム7 パスワード2 ＊再入力＊（確認用）-->

                        <p class="wf-sawarabimincho">パスワード(確認用）<span style="color:red">※必須</span></p>
                        <input type="password" name="password2" placeholder='・・・・・・' maxlength="255" value="">

                        <!-- error -->
                        <!-- もしPOSTされた時に -->
                        <?php if (!empty($error['password2'])) : ?>
                            <p class="error">* パスワードを入力してください</p>
                        <?php endif ?>

                        <?php if (!empty($_POST['password2'])) : ?>
                            <?php if (!empty($error['password2_length'])) { ?>
                                <p class="error">* パスワードを4桁以上にしてください</p>
                            <?php } elseif (!empty($error['password2_difference'])) { ?>
                                <p class="error">*1つ目のパスワードと相違しています</p>
                            <?php } ?>
                        <?php endif ?>

                        <div class="div_img3">
                            <!-- キャンセル ボタン -->
                            <dt class="wf-sawarabimincho">
                                <input type="button" value='キャンセル' style="width: 115px; height: 25px" onclick="location.href='../../login/process.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">

                                <!-- 送信ボタン -->
                                &nbsp;
                                <input type="submit" name="send" id="submit" value="入力内容を確認する" />
                            </dt>
                        </div>
                    </form>

                </div>

            </div>

        </div>

    </div>
    </div>

</body>

</html>