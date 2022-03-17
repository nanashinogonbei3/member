<?php
 
session_start();
require('../dbconnect.php');


// フォームが送信した時だけエラーチェックを走らせる
if (isset($_POST['send'])) {

    // エラーチェック項目：
 
    if (empty($_POST['last_name'])) {
        $error['last_name'] = 'blank';
    }
    if (empty($_POST['members_id'])) {
        $error['members_id'] = 'blank';
    }
    if (empty($_POST['first_name'])) {
        $error['first_name'] = 'blank';
    }
    if (empty($_POST['nickname'])) {
        $error['nickname'] = 'blank';
    }

    if (empty($_POST['phone_number'])) {
        $error['phone_number'] = 'blank';
    }
    // pw1
    // もしpwの入力があったら、
    // strlen は、入力文字数の数を返してくれます
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) <4 ) {
        $error['password'] = 'length';
        }
    }
    if (empty($_POST['password'])) {
        $error['password'] = 'blank';
    }
    // pw2
    if(!empty($_POST['password2'])){
        if (strlen($_POST['password2'] ) <4 ) {
            $error['password2'] = 'length2';
        }
    }
    if (empty($_POST['password2'])) {
        $error['password2'] = 'blank2';
    } 

    if (!empty($_POST['password2']) && !empty($_POST['password'])) {
        // // もし2番目のパスワード['password2']と1番目に入力したパスワード['password']が相違していたらエラー
        if ($_POST['password2'] !== $_POST['password']) {
            $error['password2'] = 'difference';
        }
    }
   
    // メール違反のチェック 
    if(!empty($_POST['members_id'])) { 

        if(!filter_var($_POST['members_id'], FILTER_VALIDATE_EMAIL)) {
            $error['emails'] = 'valid email';
            // メール形式違反
        } 

        $stmt = $dbh->prepare('SELECT members_id FROM members WHERE members_id = :members_id');
    
        $stmt->execute(array(':members_id' => $_POST['members_id']));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if($result > 0) {
    
        $error['email_member'] = 'duplicate';
        }    
    }  
}

if (!empty($_FILES['icon_img']['name'])) {
    $fileName = $_FILES['icon_img']['name']; 
    // name 属性と$_FILES['name属性']にしなければいけない
    // echo $fileName;
    // exit;
    // 空→$fileName = $FILES['images']を、['icon_img']（ 【name="icon_img" 】と同じにしたら成功！）

	if (!empty($fileName)) {
		//「画像が空でなければ = アップロードされていれば」＝> 画像は必須項目ではないので、なくても検査を通過しても構いません。ですが、
		// 画像がアップロードしている場合で、「正しい画像ではない場合に、チェックを走らせます」

		// アップロードした画像ファイルが、「.jpg もしくは、.gif もしくは、.png 」かファイルの下３ケタを切り取って確認しよう
		// この、サブストラファンクションを使って、$fileName, -3　は、ファイル名の「うしろ３文字の拡張子」を切り取るできるので、拡張子を検査できます
	    $ext = substr($fileName, -3);
		if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png' ) {
			$error['image'] = 'type';
            // （もしも、fileがjpg もしくはgif もしくは png(ピング)fileではなかったら、
            //  '画像タイプ'のエラーです
		}

    }

	// 画像のエラー定義
    if (empty($error['image'])) {

        // 画像ファイル変数$imageをつくり、一時保存ファイル[name属性][tmp_name] に時刻ファンクションをくっつけて
        $image = date('YmdHis') .  $fileName;
        // というように2021112315167myface.png 別人が同じファイル名で上書き重複を防ぐため
        // YmdHisで、'登録時刻' . '['name属性']['tmp_nam']' で、データベースへ登録します  
        // ['tmp_name']は一時的に保存されます

        // echo $_FILES['image']; (☆ココで$_FILES['image']が空かどうかをチェックした)
        // exit;
        // 空原因：name属性とアンマッチ(正) :$_FILES['(name属性）icon_img']でfileに値が挿入できた！

        move_uploaded_file($_FILES['icon_img']['tmp_name'],


        '../member_picture/' . $image);
        $_SESSION['personal'] = $_POST;
        $_SESSION['personal']['icon_img'] = $image;
        // $_POSTに挿入されたファイルデータが、$_SESSION['personal']に代入され、
        // $image（アップロードした画像を $_SESSION['personal']['icon_img']に代入した

     
	}

                
    // 入力にエラーが無ければ、次の会員登録確認画面に遷移する
    if (empty($error)) {
        header('Location: confirm.php');
        exit();
    }
}

    // 「確認画面（confirm.php）から「書き直す」ためにリダイレクトする
    // [書き直しボタン]がリクエストされたら、再びPOSTフォームの編集画面が表示できる// ブラウザのヒストリ機能で戻ることもできるが、入力されたデータを正しく再現するために、
    // かつ&&として、$_SESSION['personal']が、正しく設定されている時だけ表示される（つまり編集が必要な場合だけ、という事）

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

        <div class='div_p'><p class="title_font">新規会員登録</p></div>

    <div class="comprehensive">   

    <!--  新規会員登録 -->        
        <div class='inline_block_2'>

       
   
        <div class="inline_block_3">

        <div class="div_font_inline">
                <p class="p_font_rarge">新規会員登録を行います「必須」の中は必ずご入力ください</p>
        <div class="line"></div>
        </div>

<!-- フォーム -->
<!-- form action="" 空にしておく。if (empty($error)) { header(Location: confirm.php)} にするのであえてロケーションは未入力にする -->
<form action="" method="post" enctype="multipart/form-data">
<!-- enctype ="multipart/form-data" は画像アップロードする時に必要 -->


    <!-- フォーム1 会員名 -->
    <p class="wf-sawarabimincho">氏名<span style="color:red">※必須</span></p>
    <!-- maxlength= '入力できる制限文字数' -->
    <input type="text" name="last_name" size="35" placeholder = '山田'
    maxlength="255" value=""  />

    <!-- error -->       
    <?php if (!empty($error['last_name'])) : ?>
        <p class="error">*氏名を入力してください</p>
    <?php endif ?>  
    
    <!-- ------------------------------------------ -->
    <!-- フォーム1 会員なまえ -->
    <p class="wf-sawarabimincho">お名前<span style="color:red">※必須</span></p>
    <!-- maxlength= '入力できる制限文字数' -->
    <input type="text" name="first_name" size="35" placeholder = '太郎'
    maxlength="255" value="" />

    <!-- error -->          
    <?php if (!empty($error['first_name'])) : ?>
        <p class="error">*名前を入力してください</p>
    <?php endif ?> 
                    
    <!-- print(htemlspecialchard)このpint  （プリント）は、エラーが起こったときに、入力した内容を反映するためのものです -->
    <!-- フォーム2  会員ID / メールアドレス -->
    <p class="wf-sawarabimincho">会員ID / メールアドレス<span style="color:red">※必須</span></p>
    <input type="text" name="members_id" size="35" placeholder = 'recipe@gmail.com'
    
    maxlength="255" value="" />

    <!-- error -->          
    <?php if (!empty($error['members_id'])) : ?>
        <p class="error">*メールアドレスを入力してください</p>
    <?php endif ?>
    <?php if (!empty($error['emails'] )) : ?>
    <p class= "error">*有効なメールアドレスを入力してください</p>
    <?php endif ?>
    <?php if (!empty($error['email_member'])) : ?>
    <p class= "error">*入力されたメールアドレスは既に登録済みです</p>
    <?php endif ?>   
                            
                                    

    <!-- フォーム3ニックネーム -->
    <p class="wf-sawarabimincho">ニックネーム<span style="color:red">※必須</span><br /></p>  
    <input type="text" name="nickname" size="30" placeholder = 'くま吉'
    maxlength="255" value="" />

    <!-- error -->
    <?php if (!empty($error['nickname'])) : ?>
        <p class="error">*ニックネームを入力してください</p>
    <?php endif ?>            
         
                    
    <!-- フォーム4 アイコン画像 -->
    <p class="wf-sawarabimincho">アイコン画像<span style="color:red">※必須</span><br /></p>  
    <input type="file" name="icon_img" 
    maxlength="255" >


    <!-- error msg -->          
    <?php if (!empty($error['image'])) : ?>
    <p class= "error">* 写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
    <?php endif ?>
    <!-- <if (!empty($error['image'])) : > -->
    <?php if (empty($_POST['image'])) : ?>
    <!-- 確認画面からリライトして、もう一度ファイルを選びなおしてもらう時 -->
    <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
    <?php endif ?>    


    <!-- フォーム5   お電話番号 -->
    <p class="wf-sawarabimincho">お電話番号(ハイフンなし）<span style="color:red">※必須</span></p>   
    <input type="text" name="phone_number" size="30"  placeholder = '08012345678'
    maxlength="11"  >

    <!-- error -->          
    <?php if (!empty($error['phone_number'])) : ?>
    <p class="error">*電話番号を入力してください</p>
    <?php endif ?>      

        
                    
    <div class="div_font_inline">
    <div class="line"></div>
    </div>

    <!-- フォーム6パスワード-->       
            
    <p class="wf-sawarabimincho">パスワード<span style="color:red">※必須</span></p>
    <input type="password" name="password" placeholder = '・・・・・・'
    maxlength="255" >

    <!-- error -->          
    <?php if (!empty($error['password'])) : ?>
    <p class="error">*パスワードを入力してください</p>
    <?php endif ?>      


    <!-- フォーム7パスワード2＊再入力＊（確認用）-->     
            
    <p class="wf-sawarabimincho">パスワード(確認用）<span style="color:red">※必須</span></p>
    <input type="password" name="password2" placeholder = '・・・・・・'
    maxlength="255" >

    <!-- error -->
    <!-- error -->          
    <?php if (!empty($error['password2'])) : ?>
    <p class="error">*パスワードを入力してください</p>
    <?php endif ?>      
    
           
    <div class="div_img3">
    <!-- キャンセル ボタン -->
    <br>
    <dt class="wf-sawarabimincho">
    <input type="button"  value='キャンセル' style="width: 115px; height: 25px"  
    onclick="location.href='../login/join.php?id='">

    <!-- 送信ボタン -->
    <p class="wf-sawarabimincho"></p>
    <input type="submit" id="submit" name="send" value="入力内容を確認する" />
                    
</form> 
                    
</div>      

</div>

</div>
<!-- div_Comprehensive -->
</div>  
</div>

</body>
</html>

