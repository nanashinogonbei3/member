<?php 
session_start();
// 親カテゴリーの直下に、子カテゴリーを追加するフォーマット
// ユーザー作業のページです。



$_SESSION['recipe_id'] = $_GET['id'];



$id = $_GET['id'];



        try{
            
            $dt = new DateTime('now', new DateTimeZone('Asia/Tokyo'));
            $date = $dt->format('Y-m-d');

            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

            $dbh = new PDO($dsn,'root','');

            $dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


            // 親カテゴリーの一覧をプルダウン選択できるように表示
            $sql = 'SELECT parent_categories.id, parent_categories.category_name
            FROM parent_categories';

            $stmt = $dbh->prepare($sql);
        
            //SQLを実行します。
            $stmt->execute();

            $list = $stmt->fetchAll(PDO::FETCH_ASSOC);



            // 親カテゴリーと子どもカテゴリーをリレーションする
            // このレシピで、// 親カテゴリー階層の下に ログインユーザー作成した子供カテゴリーだけを取り出し
            // カテゴリー編集とDELETEを行う
            // ユーザーが作ったカテゴリーIDだけを表示する
            $sql = "SELECT categories.id, categories.categories_name, 
            categories.parent_category_id, categories.users_id, parent_categories.category_name
          
            FROM parent_categories JOIN categories ON parent_categories.id = 
            categories.parent_category_id WHERE categories.is_deleted = 0
            AND categories.users_id = '".$_SESSION['member']."' ";
            

            $stmt = $dbh->prepare($sql);
        
            //SQLを実行します。
            $stmt->execute();

            $category = $stmt->fetchAll(PDO::FETCH_ASSOC);

        



            // メンバーズ・テーブルに接続する
            $sql = 'SELECT members.id FROM members WHERE id = '.$_SESSION['member'].' ';

            $stmt = $dbh->prepare($sql);
        
            //SQLを実行します。
            $stmt->execute();

            $member = $stmt->fetch(PDO::FETCH_ASSOC);



        } catch (PDOException $e) {
            echo 'proceduresのDBに接続できません: ',  $e->getMessage(), "\n";
            echo '<pre>';
            var_dump($e);
            echo '</pre>';
            echo $e->getMessage();
            exit;
        }


        // sendボタンが押されたら
        // この場合は、form action="" にリンク先の'edit_mycategory.php'は書かないで
        if (!empty($_POST['send']) ) {

            //  エラーチェックを走らせます
            if ($_POST['categories_name'] === '') {
                $error['categories_name'] = 'blank';
            }  
            if ($_POST['parent_category_id'] === '') {
                $error['parent_category_id'] = 'blank';
            }


            if (empty($error)) {

                $_SESSION['categories'] = $_POST;

            }

            // エラーが無ければ、確認画面に遷移する
            header('Location: add_children_categories.php');
            exit(); 
          
        }  


       ?>

<!DOCTYPE html>
<html lang="jp">
    
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>新規カテゴリー作成</title>
  

    <!-- google おしゃれ日本語ひらがなフォント https://googlefonts.github.io/japanese/-->
    <link href="https://fonts.googleapis.com/earlyaccess/kokoro.css" rel="stylesheet">
    <!-- google おしゃれ日本語漢字フォント -->
    <link href="https://fonts.googleapis.com/earlyaccess/sawarabimincho.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/stylesheet2.css">
    <!-- アコーディオン チェックボックス・リスト -->
    <link rel="stylesheet" href="css/stylesheet_a.css">
    
</head>

<bod>

   <div class='inline_block_1'>

    <div class='div_p'>
        <span style="font-size:20px;color:green;">
       
        <dt class="title_font">新規カテゴリ作成</dt></span>
        
        <!-- ログアウト -->
            <div class="div_logout"><input type="button"  value='ログアウト' class="logout_btn"
                        onclick="location.href='../../logout/process.php'" >
                         <!-- /member/logout/process.php -->
            </div>
        <!-- マイページ -->
            <div class="div_logout"><input type="button"  value='マイページ' class="logout_btn"
                        onclick="location.href='../../login/process.php'" >
                         <!-- /member/logout/process.php -->
            </div>  
        <!-- みんなのレシピ -->
        <div class="div_logout"><input type="button"  value='みんなのレシピ' class="logout_btn"
                    onclick="location.href='../../top/confirm.php'" >
                        <!-- /member/top/confirm.php -->
        </div>        
    </div>
    <!-- div_p おわり -->
       

    <div class="comprehensive">

        <div class='inline_block_2_2'>

            <div class="inline_block_3_2">
 

                <div class="div_font_inline">
                    <dt class="p_font_rarge"><span style="font-size:18px">新規カテゴリーを作成する</span></dt>
                    
                    <div class="line"></div>
                    <!-- ↑区切り線 -->
                </div>
<div class="categories_comprehensive">
    <div class="div_hidari3">

<!-- フォーム -->
<form action="" method="POST"> 

    <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
    <!-- ↑ログイン・ユーザーID -->
    <input type="hidden" name="id" value="<?php echo $id ?>">    
    <!-- ↑ add_children_category.phpでINSERTしてからconfirm.phpのページにリダイレクトするために送る -->
    
    
    <dt class="wf-sawarabimincho">
    <span style="font-size:16px;"><h1>①</h1>カテゴリー名</span></dt>
            
    <!-- フォーム1 カテゴリー名の入力を行う -->

<?php if(!empty($_POST['categories_name'])) { ?>
    <input id='child_category' type="text" name="categories_name" 
    size="35" placeholder = 'カテゴリー名を入力'
    maxlength="255" value="<?php print(htmlspecialchars($_POST['categories_name'],
    ENT_QUOTES)); ?>">
<?php } else { ?>
    <input id='child_category' type="text" name="categories_name" 
    size="35" placeholder = 'カテゴリー名を入力'
    maxlength="255" value="">
<?php } ?>

    <!-- error -->
    <?php if (!empty($error['categories_name'])) : ?>
    <p class= "error">* カテゴリー名を入力してください</p>
    <?php endif ?>

    <!-- 説明文 -->
    <dt><span style="font-size:13px; color:#555555" >
    サイト上に表示されるカテゴリーの名前です</span></dt>
    <pre>&nbsp;</pre>


</div>
<!-- div_hidari おわり -->
</div>

                                                            

            <!-- フォーム2 親カテゴリー＊選択＊-->                

            <div class="div_right3">
            <!-- フォーム3 親カテゴリーを選択する ＊-->     
                
            <dt class="wf-sawarabimincho">
            <span style="font-size:16px;"><h1>②</h1>親カテゴリーを選択する</span></dt>
            <!-- カテゴリー登録フォーム -->


                    <!-- アコーディオンバーはじまり -->
                    <!-- 親カテゴリを選ぶ✅ボックス -->

                    <!-- 左側 はじまり -->
                    <div class="div_width">

                    <!-- 1 -->
                    <input id="acd-check1" class="acd-check" type="checkbox">
                    <label class="acd-label" for="acd-check1">     
                
                    親カテゴリー</label>
                    <div class="acd-content">
                 

                    <!-- 親カテゴリー -->
                    <dt>●親カテゴリー</dt>
                   
                    <?php foreach ($list as $key => $v) : ?>
                      
                    <table  class="table">
                        <tr>
                            <td><input type="radio" name="id[]" value='<?php echo $v['id'] ?>'></td>
                            <td><?php echo $v['category_name'] ?></td>
                        </tr>
                    </table>
                    <?php endforeach ?>

                    <!-- リセット -->
                    <input type="reset" value="リセット" class="btn-border">
                    </div>


                    <!-- 説明文 -->
                    <dt><span style="font-size:13px; color:#555555" >
                    カテゴリーは階層構造を持つことができます。
                    例えば、カレーという<br>カテゴリの下にはスパイスカレーという
                    子カテゴリーを作る、といったようなことです。
                    これはオプションです。</span></dt>    
        
                    </div> 
                    &nbsp;<br>               
                    </div>
                    <pre>
                    &nbsp;<br>
                    </pre>  
               
                    <!-- ボタン -->
                    <div class="bottun">
                        <!-- 新規カテゴリー送信ボタン -->
                        <dt><input type="submit"  class="update" 
                        value="新規カテゴリーの追加" name="send" style="width: 210px;
                        color: #4F5902;
                        height: 33px;
                        font-size: 16px;
                        border-radius: 10px;
                        border: none;
                        background-color: #E9C8A7;
                        background-color: #D9CC1E
                        " >
                        </dt>
                    </div>
    </form>


         
                                  
            <div class="line"></div>
            <!-- ↑区切り線 -->
                 
 <!-- 1 -->
 <div class="space3">
        <p>🔲このレシピのカテゴリー</p>
    
    <table width="350px">
    <thead>
        <tr>  
            <!-- ID -->     
            <th><dt class="wf-sawarabimincho"></th>
            <!-- カテゴリ名 -->
            <th></th>
            <!-- ✅ -->
            <th></th>
            <!-- 削除btn -->
            <th></th>
            <!-- 親カテゴリ -->
            <th></th>
            

        </tr>
    </thead>

    <tbody>

    <?php if (!empty($category)) { ?>
        <?php foreach ($category as $key => $v ) { ?>
        <tr>
            <td>・</td>
   
            <td><?php echo $v['categories_name'] ?></td>
                   <!-- 編集 -->
                <td> 
                   <!-- ユーザーの作成カテゴリーを編集する -->
                   <form method="POST" action="update_categories.php">
                        <!--  親カテゴリーの隠し送信 -->
                        <input type="hidden" name="parent_category_id" value="<?php echo $v['parent_category_id'] ?>">
                        <!--  ユーザーidの隠し送信 -->
                        <input type="hidden" name="users_id" value="<?php echo $_SESSION['member'] ?>">
                        <!--  カテゴリー名 -->
                        <input type="checkbox" name="id" value="<?php echo $v['id'] ?>">
                        <p><input type="text" name="categories_name" value="<?php echo $v['categories_name'] ?>" >
                        
                        <!--  編集 ボタン -->
                        <input type="submit" class="update" value="update" name="edit" class="execution-btn">  
                        </p>
                    </form>
                </td>

                <td>
                <!-- フォーム 登録カテゴリ削除 -->
                <form method="POST" action="update_is_del_category.php">
                    <!-- is_deleted == 1 （論理削除）の値をPOSTで隠して渡す -->
                    <input type="hidden" name="is_deleted" value="1" >
                    <!-- カテゴリー・テーブルのidを隠して渡す -->
                    <input type="hidden" name="id" value="<?php echo $v['id'] ?>">
                    
                    <input type="hidden" name="recipe_id" value="<?php echo $_SESSION['recipe_id'] ?>">
                    <!-- 削除 Delete ボタン -->
                    <input type="submit" value="Delete" name="del" class="execution-btn">                      
                </td>
                <td><span style="color:#888888"><?php echo $v['category_name'] ?></span></td>
            </form>
        </tr>

        <?php } ?> 
    </tbody>
    </table>
    <?php } else {
        echo '<dt>カテゴリーは未登録です</dt>';
    } ?>
    </div>

    <!-- 区切り線 -->
    <div class="line"></div>
            
                 


            <div class="div_font_inline">
                

               
                <!-- 戻る -->
                <input type="button" class="re-order" onclick="window.history.back();" value="前のページに戻る">
             
              
            </div>
    </div>
</div>

</body>
</html>

