<?php
session_start();


$_SESSION = array();
// セッションを空っぽにして、中の情報をログアウトすることで全部削除しますので、
// ここは、array(空っぽ);の配列で$_SESSIONを上書きします。

// セッションを消すなら、session_destroy()で消せるし、25行目のsetcookie('members_id)で、メールアドレスを消せていたので、下はいらないとのこと。
// if (ini_set('session.use_cookies')) {
// ini_set(これは、セッションにクッキーをクッキーを使うかどうかという設定するファイルのこと)
// この場合にクッキーの情報を削除するための処理をに書いていきます。
// $params = session_get_cookie_params();
// setcookie(session_name() . '' , time() - 42000,
//     $params['path'], $params['domain'], $params
//     ['secure'], $params['httponly']);
// クッキーの有効期限を切る事でセッションの情報を削除する処理を行います
// このsession_get_cookie_params()というファンクションが返してきた、こちらの値をそれぞれ指定して、
// セッションのクッキーが使っているそれぞれのオプション指定していっています。
// これによって、セッションで使ったクッキーを削除する、という事です。
// }
session_destroy();
// セッションの情報も削除します
setcookie('members_id', '', time() - 3600);
// またクッキーに保存しているメールアドレスの情報も削除します。
// ''という空の値を設定して、有効期限を切ります。
// これによってクッキーの値も消えます。


// login/join.php でログインしたときの、userIDが（["PHPSESSID"]）残っているが、メールアドレスは消えている。
// cookie 情報をCROME で確認する方法 /クロムの右上、縦に・・・を押す/その他のツール/デベロッパーツール/Aplication/cookie

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
            <dt class="title_font"></dt>
            <!-- みんなのレシピ -->
            <div class="div_logout">
                <input type="button" value='ログインしないで閲覧する' class="logout_btn" onclick="location.href='../top/index.php'">
            </div>
        </div>


        <div class="comprehensive">

            <!--  ログインする -->
            <div class='inline_block_2'>

                <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">ログアウトしました</p>
                        <div class="line"></div>
                    </div>




                    <div class="div_font_inline">
                        <input type="button" value='ログイン画面' style="width:20%;padding:10px;font-size:15px;" onclick="location.href='../login/join.php'" class="btn-border">
                    </div>

                </div>

            </div>
        </div>
    </div>

</body>

</html>