<?php 
session_start();
// 必要なファイルを読み込む
require_once('../class/db/Base.php');
require_once('../class/db/CreateRecipes.php');

$message = "ご利用ありがとうございました。またのご利用をお待ちしております。"

?>


<!DOCTYPE html>
<html lang="jp">
    
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>退会</title>
  

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">

</head>


<body>

   <div class='inline_block_1'>

        <div class='div_p'><dt class="title_font">退会</dt>
              
                        
                   
    </div>
       

    <div class="comprehensive">

        <!--  新規会員登録の確認 -->
        <div class='inline_block_2'>

            <div class="inline_block_3">

                    <div class="div_font_inline">
                        <p class="p_font_rarge">退会処理が完了しました</p>
                        <div class="line"></div>
                    </div>

              
                        <!-- メッセージ -->
                        <p class="wf-sawarabimincho"><?php echo $message ?></p>
                  

                    <!-- 新規会員登録 -->
                    <div class="div_deactivate_after">
                        <input type="button"  value='新規会員登録' style="width: 115px; height: 25px"  
                        onclick="location.href='../login/join.php?id='" class="btn-border">
                    </div>

            </div>            
                          
        </div>      
    </div>
</div>

</body>
</html>

