<?php
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';
// 定数MODEL_PATH(modelディレクトリ)のitem.phpファイルを読み込み
require_once MODEL_PATH . 'item.php';
// 定数MODEL_PATH(modelディレクトリ)のcart.phpファイルを読み込み
require_once MODEL_PATH . 'cart.php';

// ログインチェックのためセッション開始
session_start();

// ログインしていない($_SESSION変数のユーザIDが空の)場合
if(is_logined() === false){
  // ログイン画面(login.php)に遷移
  redirect_to(LOGIN_URL);
}

// データベースに接続
$db = get_db_connect();
// データベースからユーザIDを参照してユーザー情報を取得する
$user = get_login_user($db);

// user_idのカートの中身を取得
$carts = get_user_carts($db, $user['user_id']);

// トークンの確認
if(is_valid_csrf_token($_POST['csrf_token']) === false){
  // エラーメッセージ
  set_error('不正な操作です。');
  // カートページに遷移(戻る)
  redirect_to(CART_URL);
} else {
    // トークンを作り直し$_SESSION変数に保存
    $token = get_csrf_token();
}

//トランザクション開始
$db->beginTransaction();
try {
  
  // 購入履歴登録
  purchase_history($db, $carts[0]['user_id']);
  // 直前にINSERTしたIDを取得(複数購入した際のエラー防止)
  $id = $db->lastinsertId();

  foreach($carts as $cart){
    // 購入明細登録
    purchase_detail($db, $id, $cart['item_id'], $cart['price'], $cart['amount']);
    // 購入するためにカートに商品が入っていること、公開ステータス、在庫数に問題ないかチェック
    // 購入処理、在庫の更新、カートの削除
    purchase_carts($db, $carts);
    }
    // コミット
    $db->commit();
    // カート内の合計金額を代入
    $total_price = sum_carts($carts);
  

  } catch(PDOException $e) {
    // そうでない場合ロールバック
    $db->rollback();
    // どれか一つでも問題があればエラーメッセージ
    set_error('商品が購入できませんでした。');
    // カートページに遷移(戻る)
    redirect_to(CART_URL);


}

// 定数VIEW_PATH(viewディレクトリ)のfinish_view.phpファイル読み込み
include_once '../view/finish_view.php';