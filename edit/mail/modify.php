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

        $stmt = $dbh->prepare($sql);

        $stmt->execute();

        $result = $dbh->query($sql);

        $list = $result->fetchall(PDO::FETCH_ASSOC);




        // 例外が発生したときの処理
    } catch (Exception $e) {
        echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    }
} else {
    header('Location: ../../login/join.php');
    exit();
}

// フォームが送信した時だけエラーチェックを走らせる
if (!empty($_POST)) {


    // mail 1 (入力フォーム1)
    if (empty($_POST['members_id'])) {
        $error['members_id'] = 'blank';
    }
    // mail 2 (入力フォーム2 （再入力） )
    if (empty($_POST['members_id2'])) {
        $error['members_id2'] = 'blank';
    }
    // 入力フォーム1 と 入力フォーム２のメアドが異なる場合、エラーメッセージをフォーム下に親切に表示する
    if ($_POST['members_id'] !== $_POST['members_id2']) {
        $error['members_id_difference'] = 'difference';
    }


    // メール形式に違反のメールアドレスがPOSTされた時は形式エラーを表示する
    if (isset($_POST)) {

        if (!filter_var($_POST['members_id'], FILTER_VALIDATE_EMAIL)) {
            $error['members_id_valid_email'] = 'valid email';
            // メール形式違反
        } elseif (!filter_var($_POST['members_id2'], FILTER_VALIDATE_EMAIL)) {
            $error['members_id2_valid_email'] = 'valid email';
            // メール形式違反           
        } else {
            $stmt = $dbh->prepare('SELECT members_id FROM members WHERE members_id = :members_id');
            $stmt->execute(array(':members_id' => $_POST['members_id']));
            $emailcheck = $stmt->fetch(PDO::FETCH_ASSOC);

            // メールアドレスが重複
            if (!empty($emailcheck['members_id'])) {
                $error['members_id_duplicate'] = 'duplicate';
            }
        }
    }



    // 入力にエラーが無ければ、$_POST の値をセッションの、二次配列に代入して、次の会員登録確認画面に遷移する
    if (empty($error)) {

        $_SESSION['members'] = $_POST;
        header('Location: confirm.php');
        exit();
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
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>



<body>

    <div class='inline_block_1'>

        <div class='div_p'>
            <p class="title_font">会員登録の変更</p>
        </div>

        <div class="comprehensive">

            <!--  新規会員登録 -->
            <div class='inline_block_2'>


                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <?php foreach ($list as $v) : ?>
                            <!-- FETCH()された、現在のラストネーム ['last name']カラム -->
                            <div class="div_img3">
                                <dt>
                                    <span style="font-size:15px;color:green;"><?php echo $v['last_name']; ?></span>様のメールアドレスの変更
                                </dt>
                            </div>
                            <div class="line"></div>
                    </div>


                    <!-- form action="" 空にしてエラーバリデーションを表示する。if (empty($error)) { header(Location: confirm.php)} にするのであえてロケーションは未入力にする -->
                    <form action="" method="post" enctype="multipart/form-data">

                        <!-- 冒頭で代入した $id = $_GET['id'] (今ログイン中のID 'id' を隠して送る -->
                        <input type="hidden" name="id" <?php echo $id ?>>




                        <div class="div_img3">
                            <!-- FETCH()された、現在のメールアドレス members_idカラム -->
                            <dt>●現在ご登録されている、会員ID (メールアドレス)
                                <span style="font-size:13px;color:green;"><?php echo $v['members_id']; ?></span>
                            </dt>
                        </div>
                        <!-- 変更フォーム1  会員ID / (メールアドレスの変更) -->
                        <p class="wf-sawarabimincho">あたらしい会員ID / メールアドレス<span style="color:red">※必須</span></p>
                        <?php if (!empty($_SESSION['members']['members_id'])) { ?>
                            <input type="text" name="members_id" size="35" placeholder='new_mail@gmail.com' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                        $_SESSION['members']['members_id'],
                                                                                                                                        ENT_QUOTES
                                                                                                                                    )); ?>" />
                        <?php } else { ?>
                            <input type="text" name="members_id" size="35" placeholder='new_mail@gmail.com' maxlength="255" value="" />
                        <?php } ?>


                        <?php if (!empty($_POST)) : ?>
                            <?php if (!empty($error['members_id'])) { ?>
                                <p class="error">*メールアドレスを入力してください</p>
                            <?php } ?>
                        <?php endif ?>
                        <!-- 有効性 -->
                        <?php if (!empty($_POST['members_id'])) { ?>
                            <?php if (!empty($error['members_id_valid_email'])) { ?>
                                <!-- < if ($error['members_id'] === 'valid email') : > -->
                                <p class="error">*有効なメールアドレスを入力してください</p>
                            <?php } ?>
                        <?php } ?>
                        <!-- 重複性 -->
                        <?php if (!empty($_POST['members_id'])) { ?>
                            <?php if (!empty($error['members_id_duplicate'])) : ?>
                                <p class="error">* 指定されたメールアドレスは既に登録されています</p>
                            <?php endif ?>
                        <?php } ?>


                        <!-- --------------------------------------------------------------- -->
                        <!-- 変更フォーム2 （確認用）  会員ID / (メールアドレスの変更) -->
                        <?php if (!empty($_SESSION['members']['members_id2'])) { ?>
                            <p class="wf-sawarabimincho">(確認用）<span style="color:red">※必須</span></p>
                            <input type="text" name="members_id2" size="35" placeholder='new_mail@gmail.com' maxlength="255" value="<?php print(htmlspecialchars(
                                                                                                                                        $_SESSION['members']['members_id'],
                                                                                                                                        ENT_QUOTES
                                                                                                                                    )); ?>" />
                        <?php } else { ?>
                            <p class="wf-sawarabimincho">(確認用）<span style="color:red">※必須</span></p>
                            <input type="text" name="members_id2" size="35" placeholder='new_mail@gmail.com' maxlength="255" value="" />
                        <?php } ?>


                        <!-- error -->
                        <!-- POSTされた時にエラーがあれば -->
                        <!-- 未入力 -->
                        <?php if (!empty($_POST)) : ?>
                            <?php if (!empty($error['members_id2'])) { ?>
                                <p class="error">* メールアドレスを入力してください</p>
                            <?php } ?>
                        <?php endif ?>

                        <!-- 有効性 -->
                        <?php if (!empty($_POST['members_id2'])) { ?>
                            <?php if (!empty($error['members_id2_valid_email'])) { ?>
                                <p class="error">*有効なメールアドレス2を入力してください</p>
                            <?php } ?>
                        <?php } ?>

                        <!-- 重複性 -->
                        <?php if (!empty($_POST['members_id2'])) { ?>
                            <?php if (!empty($error['members_id_duplicate'])) : ?>
                                <p class="error">* 指定されたメールアドレスは既に登録されています</p>
                            <?php endif ?>
                        <?php } ?>

                        <!-- 相違 -->
                        <?php if (!empty($_POST['members_id']) && !empty($_POST['members_id2'])) { ?>
                            <?php if (!empty($error['members_id_difference'])) : ?>
                                <!-- < if ($error['members_id'] === 'difference') : > -->
                                <!-- ===にしないと、!empty($_POST['members_id'])ポストが無いときもエラー表示されちゃうので注意 -->
                                <p class="error">上段と下段のメールアドレスが異なります</p>
                            <?php endif ?>
                        <?php } ?>


                        <div class="div_img3">
                            <!-- 戻る ボタン -->
                            <dt class="wf-sawarabimincho">
                                <input type="button" value='キャンセル' style="width: 115px; height: 25px" onclick="location.href='../../login/process.php?id=<?php echo $_SESSION['id'] ?> action=rewrite'" class="btn-border">

                                <!-- 送信ボタン -->

                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="submit" value="入力内容を確認する" />
                            </dt>
                        </div>


                    </form>
                <?php endforeach ?>
                </div>

            </div>

        </div>
    </div>
    </div>

</body>

</html>