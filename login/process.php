<?php
session_start();

if (empty($_SESSION['member'])) {
    header("Location: ./join.php?id=");
    exit;
}


        try {

            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
            $date = $dt->format('Y-m-d');

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn, 'root', '');

            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // DBからFETCH（取り出し）したい(fetch＝取り出す）カラムを「recipe_note_id」を表示、「created_date」を昇順に表示させる
            $sql = 'SELECT * FROM members where id ='.$_SESSION['member'].'
            ORDER BY created_date ASC';

            $stmt = $dbh->prepare($sql);
            //execute = '実行する'
            //SQLを実行します。
            $stmt->execute();

            //ASSOCは、配列のキーを「カラム名」のみが準備されます // FETCH とは「取り出す」という意味です。
            $result = $dbh->query($sql);

            $list = $result->fetch(PDO::FETCH_ASSOC);


            foreach ($list as $key => $value) {
                if ($key === 'icon_img') {
                    $img = $value;     
                }
                if ($key === 'nickname') {
                    $nickname = $value;
                }
            }
              

            // **********

            // 住所登録済みか判定するためのDB接続。post_numberだけ出す((住所登録時は必ず郵便番号を登録している。))
            $sql = 'SELECT * FROM members where id ='.$_SESSION['member'].'
            ORDER BY created_date ASC';

            $stmt = $dbh->prepare($sql);
    
            //SQLを実行します。
            $stmt->execute();
            
            $result = $dbh->query($sql);

            $post = $result->fetch(PDO::FETCH_ASSOC);


            foreach ($post as $key => $value) {
                if ($key === 'post_number') {
                     // echo "私の郵便番号は、". "$post_number"."です。";
                    $post_number = $value;
                   
                }
            }
           

        } catch (Exception $e) {
            echo 'DBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            exit;
        }

            // セッションに記録された時間が、今の時間よりも大きい、つまりログイン時間から1時間以上たっていた場合,という意味
            if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
            // （1時間が経過していたら、）ログアウトし、ログイン画面に遷移する
            $_SESSION['time'] = time();
            // 現在の時刻で上書きします。こうすることで、何か行動したことで上書きすることで最後の時刻から１時間を記録することができるようになる。

            // $dbh は、冒頭sql文 $dbh = new PDO($dsn,'root',''); 　の$dbh変数とおなじにしなければ接続エラーになります
            $members = $dbh->prepare('SELECT * FROM members WHERE id=?');
            $members->execute(array($_SESSION['id']));
            // $members という複数形の’メンバー’のidの配列から、
            // $memberという単数形で且つ今ログインしている会員の情報をFETCH()して取り出します 
            // 現在ログイン中の○○さんの名前を表示します
            $member = $members->fetchAll();


            }

           


?>

<!DOCTYPE html>
<html lang="jp">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>マイページ</title>


<!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
<link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
<!-- google おしゃれ日本語漢字フォント -->
<link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
<link rel="stylesheet" href="css/stylesheet2.css">

</head>


<body>

<div class='inline_block_1'>

<div class='div_p'>
<dt class="title_font">マイページ</dt>
<div class="div_logout">
<input type="button" value='ログアウト' class="logout_btn" onclick="location.href='../logout/process.php'">
</div>
</div>

<div class="comprehensive">

<!--  ログインする -->
<div class='inline_block_2'>

<div class="inline_block_3">

<div class="div_font_inline">

<?php if (!empty($_SESSION['member'])) : ?>
   
        <!-- アイコン画像－現在ログインしている会員のアイコン画像 -->
        <img src="../member_picture/<?php print(htmlspecialchars(
                                        $img,
                                        ENT_QUOTES
                                    )); ?>" width="100px" height="auto">
        <!-- 現在ログインしている会員ニックネーム -->
        <dt class="p_font_rarge"><span style="font-size:13px;color:green">
                <?php print(htmlspecialchars($nickname, ENT_QUOTES)); ?></span>
            <span style="font-size:11px">さんのマイページの管理</span>
        </dt>
  
<?php endif ?>

    <div class="div_logout">
        <!-- 戻る -->
        <input type="button" class="re-order" onclick="window.history.go(-2);" value="前のページに戻る">
        <!-- ↑'history.go(-2);だと２つ前の『購入手続き』まで戻れます。 -->
        <!-- 戻るおわり -->
    </div>


    <!-- 区切り線 -->
    <div class="line"></div>
    

</div>
<!-- /////////////////////////////////////////////////////////////////////////////////////////// -->


<div class="comprehensive2">


    <!-- レシピノートを作る -->                                
    <!-- フォーム / POST送信で、ログイン中の会員、membersテーブルのID 情報を、レシピ作成画面に送る。 -->
    <form action="../create/recipe/index.php?id=<?php echo $_SESSION['member'] ?>" method="post">

        
        <dt class="wf-sawarabimincho"></dt>
        <input type="submit" id="submit" value='レシピノートを作る' style="width:120%;padding:20px;font-size:15px;" class="btn-border">

    </form>


    <!-- みんなのレシピへのリンク  -->
    <form action="../top/confirm.php?id=<?php echo $_SESSION['member'] ?>" method="post">
        <div class="div_process">
            <p class="wf-sawarabimincho"></p>
            <input type="submit" value='みんなのレシピ' style="width:120%;padding:20px;font-size:15px;" class="btn-border">
        </div>
    </form>


    <!-- お気に入りレシピへのリンク (仮）注:以下ハ仮リンク -->
    <form action="../show/favorite_recipe.php?id=<?php echo $_SESSION['member'] ?>" method="post">
        <div class="div_process">
            <p class="wf-sawarabimincho"></p>
            <input type="submit" value='お気に入りレシピ' style="width:115%;padding:20px;font-size:15px;" class="btn-border">
        </div>
    </form>


</div>
<!-- div_comprehensive-->

<br><br>
<!-- 会員情報の変更 -->
<dt class="p_font_rarge">〇会員情報の変更の変更がこちらからできます</dt>

<!-- 区切りライン 横線 -->
<div class="line"></div>

<!-- ///////////////////////////////////////////////////////////////////////////////////////////////-->


<div class="comprehensive2">


    <!-- 会員情報の変更 -->                                
    <!-- フォーム / POST送信で、ログイン中の会員の変更する会員情報を、変更画面に送る。 -->
    <form action="../edit/acount/modify.php?id=<?php echo $_SESSION['member'] ?>" method="post">

        

        <dt class="wf-sawarabimincho"></dt>
        <input type="submit" id="submit" value='会員情報を変更する' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">



    </form>

    <!-- パスワードの変更 -->
    <form action="../edit/password/modify.php?id=<?php echo $_SESSION['member'] ?>" method="post">


        
        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='パスワードの変更  ' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>

    <!-- メールアドレスの変更 -->
    <form action="../edit/mail/modify.php?id=<?php echo $_SESSION['member'] ?>" method="post">

        
        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='メールアドレスの変更' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>





<!-- div_comprehensive  -->
</div>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////-->



<br><br>
<dt>〇ご購入履歴とお届け先ご住所などがこちらから確認できます。</dt>

<div class="line"></div>

<!-- ///////////////////////////////////////////////////////////////////////////////////////////////-->
<!-- 購入履歴 -->

<div class="comprehensive3">

    <!-- 購入履歴 -->
    <form action="../show/shopping_history.php?id=<?php echo $_SESSION['member'] ?>" method="post">
        
        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='購入履歴' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>

    <!-- ****************************************************************************************** -->
    
    <!-- お気に入り商品 -->
    <form action="../show/favorite_product.php?id=<?php echo $_SESSION['member'] ?>" method="post">



        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='お気に入り商品' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>

<!-- ****************************************************************************************** -->
<!-- 配送先住所が未登録の場合「配送先住所登録」ボタンを表示する -->


            
            
    <!-- 住所登録が在れば編集ページへ遷移する -->
    <?php if (!empty($post_number)) { ?>   
    
            <!-- 住所の編集 -->
            <form action="../edit/acount/edit_address.php?id=<?php echo $_SESSION['member'] ?>" method="post">


                <div class="div_process">
                    <dt class="wf-sawarabimincho"></dt>
                    <input type="submit" id="submit" 
                    value='配送先ご住所の変更' style="width: 125%;padding:20px;font-size:15px;" class="btn-border">
                    
                </div>

            </form>
            
    <!-- 住所が未登録であれば、新規住所登録するページへ遷移する -->
    <?php } else { ?> 

            <!-- 配送先住所が未登録の場合、 -->
            <form action="../edit/acount/address.php?id=<?php echo $_SESSION['member'] ?>" method="post">

                <!-- 配送先住所を新規登録する。 -->
               

                <div class="div_process">
                    <dt class="wf-sawarabimincho"></dt>
                    <input type="submit" id="submit" value='ご住所の登録' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
                </div>

            </form>

<?php } ?> 



<!-- div_comprehensive  -->                                    
</div>


<!-- 区切りライン 横線 -->
<div class="line"></div>


<!-- ///////////////////////////////////////////////////////////////////////////////////////////////-->



<div class="comprehensive3">



    <!-- フォーム / POST送信で、ログイン中の会員の[**********]変更画面に送る。 -->
    <form action="./process.php?id=<?php echo $_SESSION['member'] ?>" method="post">

        <!-- 会員情報の変更 -->
        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='***********LINK' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>

    <!-- フォーム / POST送信で、ログイン中の会員の[**********]変更画面に送る。 -->
    <form action="./process.php?id=<?php echo $_SESSION['member'] ?>" method="post">


        <!-- パスワードの変更 -->
        <div class="div_process">
            <dt class="wf-sawarabimincho"></dt>
            <input type="submit" id="submit" value='*******LINK' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
        </div>

    </form>


    <!-- 管理者用 ページ表示 -->
    <!-- もしも、ログインメンバーID＝104(なかじん）がログインしていた場合は以下の「商品アイテム」への
    DIV（ボタン）を表示する。 -->
    <?php if (!empty($_SESSION['member'] == 104)) { ?>

        <!-- フォーム / POST送信で、管理者用[商品アイテム]の編集画面に送る。 -->
        <form action="../product/index.php?id=<?php echo $_SESSION['member'] ?>" method="post">

            <!-- 商品アイテムの編集ページへのリンク・ボタン -->
            <div class="div_process">
                <dt class="wf-sawarabimincho"></dt>
                <input type="submit" id="submit" value='商品アイテムの編集' style="width: 120%;padding:20px;font-size:15px;" class="btn-border">
            </div>

        </form>
    <?php } elseif ($_SESSION['member'] !== 104) { ?>
        <!-- 何も表示しない -->
    <?php } ?>

</div>
<!-- div_comprehensive  -->


<!-- ///////////////////////////////////////////////////////////////////////////////////////////////-->
<!-- フォーム / POST送信で、ログイン中の会員の変更する会員情報を、変更画面に送る。 -->
<form action="../deactivate/unsubscribe.php?id=<?php echo $_SESSION['member'] ?>" method="post">

    <div class="div_deactivate">
        <input type="submit" value='退会する' class="unsubscribe_btn" style="
                    font-size: 13px;
                    width: 100px;
                    height: 35px; 
                    border-radius: 5px;
                    
                    color: #ffffff;
                    background: #000000;">
    </div>
</form>


</div>
</div>

</body>

</html>