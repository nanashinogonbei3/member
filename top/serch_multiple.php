                    <?php
                    // このファイルでは、✅ボックスで複数選択された場合に、implode関数を使って、文字列8，9と読み込み、
                    // リレーションした調理手順の結果を渡す準備をします

                    session_start();
                    // 必要なファイルを読み込む
                    require_once('../class/db/Base.php');
                    require_once('../class/db/CreateRecipes.php');


                    // 送信データのカテゴリーIDを受け取る
                    $categoryid = $_GET['category_id'];


                    if (empty($_GET['category_id'])) {
                        // フォーム未入力ならリダイレクト
                        header("Location: ./confirm.php?id=");
                        exit();
                    } else {
                        $errors = array(); //ここで$errorsを初期化！
                        // エラーチェック
                        if ($_GET === " ") {
                            $errors['get'] = 'blank';
                            $_SESSION['get'] = $errors['get'];
                        }
                    }

                    // もしエラーが無ければ、sql文の以下を実行する
                    if (empty($error)) {

                        try {


                            $dsn = 'mysql:dbname=recipes;host=localhost;charset=utf8';

                            $dbh = new PDO($dsn, 'root', '');

                            // $categoryIds= count($categoryid);
                            // 実行結果 ok INT(3) = 3ヶ選択
                            // implodeは、配列要素を連結する・便利
                            $a = implode("','", $categoryid);
                        



                            // $sql = "select * from member where category in ($a) AND age in ($b) order by date desc";
                            // 参考URLhttps://oshiete.goo.ne.jp/qa/2529035.html/
                            // 参考URL(PHP manual)/ https://www.php.net/manual/ja/function.implode.php

                            $sql = "SELECT distinct
                                        my_recipes.id, my_recipes.recipe_name, my_recipes.complete_img,
                                        categories.categories_name, recipe_categories.category_id,
                                        members.nickname, categories.categories_name 
                                    FROM
                                    my_recipes
                                    JOIN members ON my_recipes.members_id = members.id
                                    left  JOIN recipe_categories ON my_recipes.id = recipe_categories.my_recipe_id
                                    left  JOIN categories on categories.id = recipe_categories.category_id
                                    WHERE my_recipes.is_released = 1 AND my_recipes.is_deleted = 0 
                                    AND categories.id IN ('" . $a . "')  ";

                            // 50行目で文字列をカンマ区切りにした配列を、60行目でINでカンマ区切りを表示する
                            // where id in (1,2,3,4,5)
                            // sqlmyAdmin で確認するときは、IN (7,9)


                            $stmt = $dbh->prepare($sql);


                            $stmt->execute();


                            $result = $dbh->query($sql);


                            $list = $result->fetchAll(PDO::FETCH_ASSOC);

                        

                            if (!empty($list)) {

                                // 検索したFETCHしたレシピIDとカテゴリーIDをセッションに渡す
                                $_SESSION['multiple_id'] = $list;

                            
                                header("Location: ./acodion.php?id=");
                                // DB登録処理完了後、インデックスページ（index.php）へリダイレクト
                                exit;

                            } elseif (empty($list)) {

                                // もり未ログイン状態なら
                                if (empty($_SESSION['member'])) {
                                    // エラーメッセージを渡す
                                    $error2 = 'このカテゴリのレシピは未登録です。';
                                    $_SESSION['error2'] = $error2;
                                    // index.php へ遷移する
                                    header("Location: ./index.php?id=");
                                    
                                    exit;

                                // もしログイン状態なら
                                } elseif (!empty($_SESSION['member'])) {
                                    // エラーメッセージを渡す
                                    $error2 = 'このカテゴリのレシピは未登録です。';
                                    $_SESSION['error2'] = $error2;
                                    // confirm.php へ遷移する
                                    header("Location: ./confirm.php?id=");
                                    
                                    exit;
                                } 

                            }



                        } catch (PDOException $e) {
                            echo 'categoriesのDBに接続できません: ',  $e->getMessage(), "\n";
                            echo '<pre>';
                            var_dump($e);
                            echo '</pre>';
                        
                            exit;
                        }
                    }
?>