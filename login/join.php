<?php
session_start();
require('../dbconnect.php');

// クッキーにログインメールアドレスを記録する
if (isset($_COOKIE['members_id']) && $_COOKIE['members_id'] !== '') {
    $members_id = $_COOKIE['members_id'];
}



try {

    $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
    $date = $dt->format('Y-m-d');

    $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

    $dbh = new PDO($dsn, 'root', '');

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // DBからFETCH（取り出し）したい(fetch＝取り出す）カラムを「members_id」を表示、「created_date」を昇順に表示させる
    $sql = 'SELECT * FROM members ';
    $sql .= 'ORDER BY created_date ASC';

    $stmt = $dbh->prepare($sql);
    //execute = '実行する'
    //SQLを実行します。
    $stmt->execute();

    //ASSOCは、配列のキーを「カラム名」のみが準備されます // FETCH とは「取り出す」という意味です
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo 'DBに接続できません: ',  $e->getMessage(), "\n";
    echo '<pre>';
    var_dump($e);
    echo '</pre>';
    exit;
}



// $_POST が空ではない＝
// フォームが送信した時だけエラーチェックを走らせる
if (!empty($_POST)) {
    // フォームのメールアドレス（members_idカラム）が、空で無ければ、
    $members_id = $_POST['members_id'];
    // クッキーに保存するメールアドレス（$members_id） は、POSTにフォーム入力したメールアドレスで上書きしてあげましょう　そうすればクッキーのemailも上書き保存されます

    if ($_POST['members_id'] !== '' && $_POST['password'] !== '') {
        // レシピノートID(メアド) と、パスワードが空欄だった時はエラーチェックをする
        // ’メアドが入力されていません’、や’パスワードが入力されていません’と丁寧に表示しない場合は、
        // if ($_POST['members_id'] !== '' && $_POST['password'] !== '') {
        // メアド・パスワードが「空ではなかったら!==''」だけで済ませる方法もあります
        if ($_POST['members_id']  === '') {
            $error['members_id']  = 'blank';
            // メールアドレスが空だったら、$error['members_id]='blank' というエラーを出力します
        }

        if (strlen($_POST['password']) < 4) {
            $error['password'] = 'length';
            // ここでは登録ではないので「（単に、）パスワードの記述がおかしい（4桁以下は登録できませんぞ！）というエラーを出力します
        }
        if ($_POST['password'] === '') {
            $error['password'] = 'blank';
            // ここでは、パスワードが空だったら、パスワードが入っていないぞ、というエラーを出力します
        }
        //  prepare = 準備
        $login = $dbh->prepare('SELECT * FROM members WHERE members_id=? 
                    -- members_id  （メールアドレスのカラム）
                    AND password=?');

        $login->execute(array(
            $_POST['members_id'],
            sha1($_POST['password'])
            // 今入力される生のパスワードと、DBの暗号化されたパスワード(sha1 不可逆型パスワード元に戻せない強力な暗号化
            // 必ず暗号化された文字列になるという特性を持ちます。だから今入力される生のPWの文字もsh1で暗号化してDBの文字列と照合します。)
        ));
        $member = $login->fetch(PDO::FETCH_ASSOC);

        // ログインメンバーIDをセッションに格納する
        if (!empty($member['id'])) {
            $_SESSION['member'] = $member['id'];
        }


        // fetchでDBから取り出して、データが返ってくれば、ログインに成功、返ってこなければログインに失敗、という事になります
        if ($member) {
            // ログインに成功したら(データベースからメアドとパスワードを取り出せたら）次のページへ遷移させる
            $_SESSION['id'] = $member['id'];
            // メンバーのidカラムが、セッションのidに誰がログインしたのかをセッションidに記録されます。
            $_SESSION['time'] = time();
            // time();ログインした時刻が、セッションのtimeという変数に記録される

            // ログイン情報をクッキーに保存する 14日間保存する google chrome 開発者ツール開く/その他のツール/Aplication/PHPSSID （ログイン時にセッションに記録されたPW
            // PHPSSID のところにemail そしてValue にmail-addressが14日間記録されます

            // ログイン画面で、✅次回からは自動的にログインする、にチェックが入っていたら＝$_POST['チェックボックス　 name="save"']
            // <input id="save" type="checkbox" name="save" 
            //                         value="on">

            if ($_POST['save'] === 'on') {
                setcookie('members_id', $_POST['members_id'], time() + 60 * 60 * 24 * 14);
                // このサイトでは、メールアドレスは、members_id カラムに入るので、$_POST['members_id]にしておく
            }

            // ログインが成功したら、マイページに遷移する。
            header('Location: ./process.php');
            exit;
            

        // ログインに失敗    
        } else {
            $error['login'] = 'failed';
            
        }
    } else {
        // メールアドレスとパスワードが未入力
        $error['login'] = 'blank';
        
    }
}



?>

<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>ログイン画面</title>


    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">
 
</head>



<body>


    <div class='inline_block_1'>

        <div class='div_p'>
            <!-- <p class="title_font">ログイン</p> -->
            <div class="div_logout">
            <input type="button" value='ログインしないで閲覧する' class="button" onclick="location.href='../top/index.php'">
            </div>
        </div>


        <div class="comprehensive">

            <!--  ログインする -->
            <div class='inline_block_2'>

                <div class="inline_block_3">


                    <div class="div_font_inline">
                        <p class="p_font_rarge">ログインIDとパスワードの入力</p>
                        <div class="line"></div>
                    </div>

                    <form action="" method="post">
                        <!-- フォーム1レシピノートID / メールアドレス ＊入力＊ -->

                        <p class="wf-sawarabimincho">ID:e-mail<span style="color:red"></span></p>

                        <input type="text" name="members_id" size="35" placeholder='recipe@iamil.com' maxlength="255" />

                        <!-- error -->
                        <?php if (!empty($error['members_id'])) : ?>
                            <p class="error">* メールアドレスを入力してください</p>
                        <?php endif ?>


                        <!-- フォーム2 パスワード＊入力＊-->
                        <p class="wf-sawarabimincho">password<span style="color:red"></span></p>
                        <input type="password" name="password" placeholder='・・・・・・' maxlength="255">

                        <!-- error -->
                        <?php if (!empty($error['login'])) : ?>
                            <p class="error">* メールアドレスとパスワードをご入力ください</p>
                        <?php elseif (!empty($error['login'])) : ?>
                            <p class="error"> "ログインに失敗しました。正しくご入力ください"</p>
                        <?php endif ?>

                        <!-- ログイン情報の記録 クッキーにメールアドレスを記録する　-->
                        <p><input id="save" type="checkbox" name="save" value="on">
                            <label for="save">次回からは自動的にログインする</label>
                        </p>


                        <!-- 送信ボタン -->
                        <p class="wf-sawarabimincho"></p>
                        <input type="submit" value="ログイン" id="submit" />

                    </form>


                    <div class="div_font_inline">
                        <p class="p_font_rarge">初めてご利用の方</p>
                        <div class="line"></div>
                        <p class="p_font_rarge">初めての方は、ここから会員登録を行ってください</p>
                    </div>


                    

                    <!-- 送信ボタン -->
                    <p class="wf-sawarabimincho"></p>
                    <input type="button" value='新規会員登録' style="width: 115px; height: 25px" onclick="location.href='../resistration/new.php'" class="btn-border">
                </div>

            </div>
        </div>
    </div>

</body>

</html>