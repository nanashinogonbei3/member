<?php
// *********｛「マイ・レシピ」テーブル ｝***********************************************************

/**
 * my_recipesテーブルクラス
 */
class MyRecipes extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from my_recipes ORDER BY created_date ASC';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**


     * レコードをアップデートする
     *
     * @param int $id
     * @param string $recipe_name
     * @param int $members_id 
     * @param int $product_id
     * @param string $complete_img
     * @param int $cooking_time
     * @param int $cost
     * @param int $how_many_servings

     * @param string $created_date
     * @param string $update_time
     * @param string $is_released
     * @param int $is_deleted
     * @param int $id_select
     * @return bool
     */
    public function update(int $id, string $recipe_name,
    int $members_id, int $product_id, string $complete_img, int $cooking_time, int $cost, int $how_many_servings, 
    string $created_date, string $update_time, string $is_released, int $is_deleted, int $id_select)
    {
        // レコードをアップデートするSQL文
        $sql = 'update my_recipes set recipe_name=:recipe_name,
        members_id=:members_id, product_id=:product_id, complete_img=:complete_img, cooking_time=:cooking_time, cost=:cost, 
        how_many_servings=:how_many_servings, created_date=:created_date, update_time=:update_time, is_released=:is_released,
        is_deleted=:is_deleted, id_select=:id_select ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);
        

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_name', $recipe_name, PDO::PARAM_STR);
        $stmt->bindValue(':members_id',$members_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id',$product_id, PDO::PARAM_INT);
        $stmt->bindValue(':complete_img',$complete_img, PDO::PARAM_STR);
        $stmt->bindValue(':cooking_time',$cooking_time, PDO::PARAM_INT);
        $stmt->bindValue(':cost',$cost, PDO::PARAM_INT);
        $stmt->bindValue(':how_many_servings',$how_many_servings, PDO::PARAM_INT);
  
        $stmt->bindValue(':created_date',$created_date, PDO::PARAM_STR);
        $stmt->bindValue(':update_time',$update_time, PDO::PARAM_STR);
        $stmt->bindValue(':is_released',$is_released, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted',$is_deleted, PDO::PARAM_INT);
        $stmt->bindValue(':id_select',$id_select, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを完了フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */

  
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update my_recipes set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from my_recipes where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
  
     * @param string $recipe_name
     * @param integer $members_id
     * @param integer $product_id
     * @param string $complete_img
     * @param integer $cooking_time
     * @param integer $cost
     * @param integer $how_many_servings

     * @param string $created_date
     * @return void
     */
    // add.phpのインサート文の数と、このインサート文の引数の数があっていないとエラー
    public function insert(string $recipe_name, int $members_id,
    int $product_id,
    string $complete_img, int $cooking_time, int $cost, int $how_many_servings, 
    string $created_date)
    {
        $sql = 'insert into my_recipes (';
        $sql .= 'recipe_name,';
        $sql .= 'members_id,';
        $sql .= 'product_id,';
        $sql .= 'complete_img,';
        $sql .= 'cooking_time,';
        $sql .= 'cost,';
        $sql .= 'how_many_servings,';
  
        $sql .= 'created_date';
        // $sql .= 'update_time,';
        // $sql .= 'release_date';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':recipe_name,';
        $sql .= ':members_id,';
        $sql .= ':product_id,';
        $sql .= ':complete_img,';
        $sql .= ':cooking_time,';
        $sql .= ':cost,';
        $sql .= ':how_many_servings,';

        $sql .= ':created_date';
        // $sql .= ':update_time,';
        // $sql .= ':release_date';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
    
        // $stmt->bindValue(':id', $_SESSION['recipe']['id'], PDO::PARAM_INT);
        $stmt->bindValue(':recipe_name', $_SESSION['recipe']["recipe_name"], PDO::PARAM_STR);
        $stmt->bindValue(':members_id',$_SESSION['recipe']['members_id'],PDO::PARAM_INT);
        $stmt->bindValue(':complete_img',$_SESSION['recipe']['complete_img'],PDO::PARAM_STR);
        $stmt->bindValue(':product_id',$_SESSION['recipe']['product_id'],PDO::PARAM_INT);
        $stmt->bindValue(':cooking_time',$_SESSION['recipe']['cooking_time'],PDO::PARAM_INT);
        $stmt->bindValue(':cost',$_SESSION['recipe']['cost'],PDO::PARAM_INT);
        $stmt->bindValue(':how_many_servings',$_SESSION['recipe']['how_many_servings'],PDO::PARAM_INT);
 
        $stmt->bindValue(':created_date',$_SESSION['recipe']['created_date'],PDO::PARAM_STR);
        // $stmt->bindValue(':update_time',$_SESSION['recipe']['update_time'],PDO::PARAM_STR);
        // $stmt->bindValue(':release_date',$_SESSION['recipe']['release_date'],PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted',$_SESSION['recipe']['is_deleted'],PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();

    }
}

// *********｛「調理手順」テーブル ｝***********************************************************

/**
 * proceduresテーブルクラス
 */
class Procedures extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from procedures order by created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param int $recipeId
     * @param string $img
     * @param string $description
     * @param string $createdDate
     * @param string $updateDate
     * @param int $isDeleted
     * @return bool
     */
    public function update(int $id, int $recipeId, string $img, string $description, string $createdDate, string $updateDate, 
    int $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update procedures set recipe_id=:recipe_id, img=:img, description=:description ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $recipeId, PDO::PARAM_INT);
        $stmt->bindValue(':img', $img, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':created_date', $createdDate, PDO::PARAM_STR);
        $stmt->bindValue(':update_time', $updateDate, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update procedures set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from procedures where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
     *
     * @param integer $recipe_id
     * @param string $img
     * @param string $description
     * @param string $created_date
     * @param string $update_time
     * @param integer $is_deleted
     * @return void
     */
    // add.php のインサート文と、このインサートの引数の数が一致していないとエラー
    public function insert(string $img, string $description)
    {
        $sql = 'insert into procedures (';
        // $sql .= 'recipe_id,';
        $sql .= 'img,';
        $sql .= 'description';
        // $sql .= 'created_date,';
        // $sql .= 'update_time,';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        // $sql .= ':recipe_id,';
        $sql .= ':img,';
        $sql .= ':description';
        // $sql .= ':created_date,';
        // $sql .= ':update_time,';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        // $stmt->bindValue(':recipe_id', recipeId, PDO::PARAM_INT);
        $stmt->bindValue(':img', $_SESSION['procedures']['img'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $_SESSION['procedures']['description'], PDO::PARAM_STR);
        // $stmt->bindValue(':created_date', $_SESSION['recipe']['createdDate'], PDO::PARAM_STR);
        // $stmt->bindValue(':update_time', $updateTime, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}

// *********｛「材料」テーブル ｝***********************************************************

/**
 * materialテーブルクラス
 */
class Materials extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from materials order by created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param int $recipe_id
     * @param int $parent_material_category_id
     * @param string $material_name
     * @param string $amount
     * @param string $created_date
     * @param string $update_date
     * @param int $is_deleted
     * @return bool
     */
    public function update(int $id, int $recipe_id, int $parent_material_category_id,
    string $material_name, string $amount, string $created_date, string $update_date, 
    int $is_deleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update materials set recipe_id=:recipe_id, parent_material_category_id = :parent_material_category_id,
        material_name=:material_name, 
        amount=:amount, created_date=:created_date, 
        update_date=:update_date, is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt->bindValue(':parent_material_category_id', $parent_material_category_id, PDO::PARAM_INT);
        $stmt->bindValue(':material_name', $material_name, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':created_date', $created_date, PDO::PARAM_STR);
        $stmt->bindValue(':update_date', $update_date, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $is_deleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $is_deleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update materials set is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':is_deleted', (int) $is_deleted, PDO::PARAM_INT);


        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from materials where id=:id ';
   

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
     *
     * @param integer $recipe_id
     * @param integer $parent_material_category_id
     * @param string $material_name
     * @param string $amount
     * @param string $created_date
     * @param string $update_time
     * @param integer $is_deleted
     * @return void
     */
    public function insert(int $recipe_id, int $parent_material_category_id, string $material_name, 
    string $amount, string $created_date, 
    string $update_time, int $is_deleted = 0)
    {
        $sql = 'insert into materials (';
        $sql .= 'recipe_id,';
        $sql .= 'parent_material_category_id,';
        $sql .= 'material_name,';
        $sql .= 'amount,';
        $sql .= 'created_date,';
        $sql .= 'update_time,';
        $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':recipe_id,';
        $sql .= ':parent_material_category_id,';
        $sql .= ':material_name,';
        $sql .= ':amount,';
        $sql .= ':created_date,';
        $sql .= ':update_time,';
        $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':recipe_id', $recipe_id, PDO::PARAM_INT);
        $stmt->bindValue(':parent_material_category_id', $parent_material_category_id, PDO::PARAM_INT);
        $stmt->bindValue(':material_name', $material_name, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':created_date', $created_date, PDO::PARAM_STR);
        $stmt->bindValue(':update_time', $update_time, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}

// *********｛[会員 / メンバーズ」テーブル ｝***********************************************************

/**
 * proceduresテーブルクラス
 */
class Members extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from members order by created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param string $last_name
     * @param string $members_id
     * @param string $nickname
     * @param string $icon_img
     * @param int $phone_number
     * @param string $password
     * @param int $post_number
     * @param string $address
     * @param string $created_date
     * @param string $update_date
     * @param int $is_deleted
     * @return bool
     */
    public function update(int $id, string $last_name, string $members_id, string $icon_img, int $phone_number, string $password, int $post_number, 
    string $address, string $created_date, string $update_date, int $is_deleted) 

    {
        // レコードをアップデートするSQL文
        $sql = 'update members set last_name=:last_name, members_id=:members_id, icon_img=:icon_img, phone_number=:phone_number, password=:password,
        post_number=:post_number, address=:address, created_date=:created_date, update_date=:update_date, is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindValue(':members_id', $members_id, PDO::PARAM_STR);
        $stmt->bindValue(':icon_img', $icon_img, PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $phone_number, PDO::PARAM_INT);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $stmt->bindValue(':post_number', $post_number, PDO::PARAM_INT);
        $stmt->bindValue(':address', $address, PDO::PARAM_STR);
        $stmt->bindValue(':created_date', $created_date, PDO::PARAM_STR);
        $stmt->bindValue(':update_date', $update_date, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $is_deleted, PDO::PARAM_INT);
       
  

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update members set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from members where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
     *
     * @param string $last_name
     * @param string $members_id
     * @param string $nickname
     * @param string $icon_img
     * @param integer $phone_number
     * @param string $password
     * @param integer $post_number
     * @param string $address
     * @param string $created_date
     * @param string $update_date
     * @param integer $is_deleted
     * @return void
     */
    // add.php のインサート文と、このインサートの引数の数が一致していないとエラー
    public function insert(string $last_name, string $members_id, string $nickname, int $phone_number,
    string $password, int $post_number, string $address)
    {
        $sql = 'insert into members (';
        $sql .= 'last_name,';
        $sql .= 'members_id,';
        $sql .= 'nickname,';
        $sql .= 'icon_img,';
        $sql .= 'phone_number,';
        $sql .= 'password,';
        $sql .= 'post_number,';
        $sql .= 'address'; 
        // $sql .= 'created_date,';
        // $sql .= 'update_time,';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':last_name,';
        $sql .= ':members_id,';
        $sql .= ':nickname,';
        $sql .= ':icon_img,';
        $sql .= ':phone_number,';
        $sql .= ':password,';
        $sql .= ':post_number,';
        $sql .= ':address'; 
        // $sql .= ':created_date,';
        // $sql .= ':update_time,';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        // $stmt->bindValue(':recipe_id', recipeId, PDO::PARAM_INT);
        $stmt->bindValue(':last_name', $_SESSION['members']['last_name'], PDO::PARAM_STR);
        $stmt->bindValue(':members_id', $_SESSION['members']['members_id'], PDO::PARAM_STR);
        $stmt->bindValue(':nickname', $_SESSION['members']['nickname'], PDO::PARAM_STR);
        $stmt->bindValue(':icon_img', $_SESSION['members']['icon_img'], PDO::PARAM_STR);
        $stmt->bindValue(':phone_number', $_SESSION['members']['phone_number'], PDO::PARAM_INT);
        $stmt->bindValue(':password', $_SESSION['members']['password'], PDO::PARAM_STR);
        $stmt->bindValue(':post_number', $_SESSION['members']['post_number'], PDO::PARAM_INT);
        $stmt->bindValue(':address', $_SESSION['members']['address'], PDO::PARAM_STR);
        // $stmt->bindValue(':created_date', $_SESSION['recipe']['createdDate'], PDO::PARAM_STR);
        // $stmt->bindValue(':update_time', $updateTime, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}



// *********｛商品リストテーブル ｝***********************************************************

/**
 * product_list テーブルクラス
 */
class Product_lists extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from product_lists order by created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
/**


     * レコードをアップデートする
     *
     * @param int $id
     * @param string $product_name
     * @param string $img
     * @param string $amount
     * @param string $coo
     * @param string $categorie_name
     * @param string $handling_start_date
   
     
    
 
   
    
     * @param string $handling_start_date
  
     * @return bool
     */
    public function update(int $id, string $product_name, string $img,  string $amount, string $coo, string $categorie_name, 
    string $handling_start_date
    )
    {
        // レコードをアップデートするSQL文
        $sql = 'update product_lists set product_name=:product_name, img=:img, amount=:amount, coo=:coo, categorie_name=:categorie_name,
        handling_start_date=:handling_start_date';
        $sql .= 'where id=:id';

        // public function update(int $id, string $product_name, string $img,  string $amount, string $coo, string $categorie_name, int $maker_id, int $sales_price, int $cost_price,
        // string $describes, string $efficacy, string $howto_use,  string $handling_start_date,
        // int $is_released, string $update_date, int $is_deleted, int $sales, int $stock)
        // {    

      // レコードをアップデートするSQL文
    //   $sql = 'update product_lists set product_name=:product_name, img=:img, amount=:amount, coo=:coo, categorie_name=:categorie_name, maker_id=:maker_id,
    //   sales_price=:sales_price, cost_price=:cost_price,  describes=:describes, efficacy=:efficacy, howto_use=:howto_use, handling_start_date=:handling_start_date,
    //   is_released=:is_released, update_date=:update_date, is_deleted=:is_deleted, sales=:sales, stock=:stock ';
    //   $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);
        

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':product_name', $product_name, PDO::PARAM_STR);
        $stmt->bindValue(':img',$img, PDO::PARAM_STR);
        $stmt->bindValue(':amount',$amount, PDO::PARAM_STR);
        $stmt->bindValue(':coo',$coo, PDO::PARAM_STR);
        $stmt->bindValue(':categorie_name',$categorie_name, PDO::PARAM_STR);
        // $stmt->bindValue(':maker_id',$maker_id, PDO::PARAM_INT);
        
        
        // $stmt->bindValue(':sales_price',$sales_price, PDO::PARAM_INT);
        // $stmt->bindValue(':cost_price',$cost_price, PDO::PARAM_INT);
        
       
        // $stmt->bindValue(':describes',$describes, PDO::PARAM_STR);
        // $stmt->bindValue(':efficacy',$efficacy, PDO::PARAM_STR);
        // $stmt->bindValue(':howto_use',$howto_use, PDO::PARAM_STR);
        
        $stmt->bindValue(':handling_start_date',$handling_start_date, PDO::PARAM_STR);
        // $stmt->bindValue(':is_released',$is_released, PDO::PARAM_INT);
        // $stmt->bindValue(':update_date',$update_date, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted',$is_deleted, PDO::PARAM_INT);
        // $stmt->bindValue(':sales',$sales, PDO::PARAM_INT);
        // $stmt->bindValue(':stock',$stock, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを完了フラグを切り替える
     *
     * @param int $id
     * @param bool $isDeleted
     * @return void
     */

  
    public function updateIsCompletedByID(int $id, bool $isDeleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update product_lists set is_deleted=:isDeleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':isDeleted', (int) $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from product_lists where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
   
    /**
     * 新規レコードをインサートする
  
    * @param string $product_name
    * @param string $img
    * @param string $amount
    * @param string $coo
    * @param string $categorie_name
  
    * @param string $handling_start_date
     * @return void
     */
    // add.phpのインサート文の数と、このインサート文の引数の数があっていないとエラー
    // 'img, ' のカンマが無くてもエラーになるので注意
    public function insert(string $product_name, string $img, string $amount, string $coo, string $categorie_name,
    string $handling_start_date)
    {
        $sql = 'insert into product_lists (';
        $sql .= 'product_name,';
        $sql .= 'img,';
        $sql .= 'amount,';
        $sql .= 'coo,';
        $sql .= 'categorie_name,';
        $sql .= 'handling_start_date';
 
    
        $sql .= ') values (';
        $sql .= ':product_name,';
        $sql .= ':img,';
        $sql .= ':amount,';
        $sql .= ':coo,';
        $sql .= ':categorie_name,';
        $sql .= ':handling_start_date';
      
     
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
    
        // $stmt->bindValue(':id', $_SESSION['recipe']['id'], PDO::PARAM_INT);
        $stmt->bindValue(':product_name', $_SESSION['product']["product_name"], PDO::PARAM_STR);
        $stmt->bindValue(':img',$_SESSION['product']['img'],PDO::PARAM_STR);
        $stmt->bindValue(':amount',$_SESSION['product']['amount'],PDO::PARAM_STR);
        $stmt->bindValue(':coo',$_SESSION['product']['coo'],PDO::PARAM_STR);
        $stmt->bindValue(':categorie_name',$_SESSION['product']['categorie_name'],PDO::PARAM_STR);
        $stmt->bindValue(':handling_start_date',$_SESSION['product']['handling_start_date'],PDO::PARAM_STR);


        // SQLを実行する
        $stmt->execute();

    }
}

// *********[ カテゴリー・テーブル ]***********************************************************

/**
 * categorie テーブルクラス
 */
class Categories extends Base
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        // 親クラスのコンストラクタの呼び出し
        parent::__construct();
    }

    /**
     * レコードを全件取得する（期限日の古いものから並び替える）
     *
     * @return array
     */
    public function selectAll()
    {
        $sql = 'select * from categories order by created_date';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQLを実行する
        $stmt->execute();

        // 取得したレコードを連想配列として返却する
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * レコードをアップデートする
     *
     * @param int $id
     * @param int $categorie_name
     * @param string $users_id
     * @param string $created_date
     * @param string $update_date
     * @param int $is_deleted
     * @return bool
     */
    public function update(int $id, string $categorie_name, int $users_id, string $created_date, string $update_date, 
    int $is_deleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update categories set categorie_name=:categorie_name, users_id=:users_id, 
        created_date=:created_date, update_date=:update_date, is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':categorie_name', $categorie_name, PDO::PARAM_STR);
        $stmt->bindValue(':users_id', $users_id, PDO::PARAM_INT);
        $stmt->bindValue(':created_date', $created_date, PDO::PARAM_STR);
        $stmt->bindValue(':update_date', $update_date, PDO::PARAM_STR);
        $stmt->bindValue(':is_deleted', $is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除フラグを切り替える
     *
     * @param int $id
     * @param bool $is_deleted
     * @return void
     */
    public function updateIsCompletedByID(int $id, bool $is_deleted)
    {
        // レコードをアップデートするSQL文
        $sql = 'update categories set is_deleted=:is_deleted ';
        $sql .= 'where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':is_deleted', (int) $is_deleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 指定IDのレコードを削除する
     *
     * @param integer $id
     * @return void
     */
    public function delete(int $id)
    {
        $sql = 'delete from categories where id=:id';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }

    /**
     * 新規レコードをインサートする
     *
     * @param integer $id
     * @param string $categorie_name
     * @param integer $users_id
     * @param string $created_date
     * @param string $update_date
     * @param integer $is_deleted
     * @return void
     */
    // add.php のインサート文と、このインサートの引数の数が一致していないとエラー
    public function insert(string $categorie_name, int $users_id)
    {
        $sql = 'insert into categories (';
        $sql .= 'categorie_name,';
        $sql .= 'users_id';
        // $sql .= 'created_date,';
        // $sql .= 'update_time,';
        // $sql .= 'is_deleted';
        $sql .= ') values (';
        $sql .= ':categorie_name,';
        $sql .= ':users_id';
        // $sql .= ':created_date,';
        // $sql .= ':update_time,';
        // $sql .= ':is_deleted';
        $sql .= ')';

        // SQL文を実行する準備
        $stmt = $this->dbh->prepare($sql);

        // SQL文の該当箇所に、変数の値を割り当て（バインド）する
        $stmt->bindValue(':categorie_name', $_SESSION['categories']['categorie_name'], PDO::PARAM_STR);
        $stmt->bindValue(':users_id', $_SESSION['categories']['users_id'], PDO::PARAM_INT);
        // $stmt->bindValue(':created_date', $_SESSION['recipe']['createdDate'], PDO::PARAM_STR);
        // $stmt->bindValue(':update_time', $updateTime, PDO::PARAM_STR);
        // $stmt->bindValue(':is_deleted', $isDeleted, PDO::PARAM_INT);

        // SQLを実行する
        $stmt->execute();
    }
}
