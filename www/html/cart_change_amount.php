<?php
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATHのfunction.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATHのuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';
// 定数MODEL_PATHのitem.phpファイルを読み込み
require_once MODEL_PATH . 'item.php';
// 定数MODEL_PATHのcart.phpファイルを読み込み
require_once MODEL_PATH . 'cart.php';
// ログインチェックのためセッションスタート
session_start();
// ログインしていない場合($_SESSION変数が空)
if(is_logined() === false){
  // ログインページに遷移
  redirect_to(LOGIN_URL);
}


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


// データベースに接続
$db = get_db_connect();
// データベースからユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// issetで中身を確認したcart_idを代入
$cart_id = get_post('cart_id');
// issetで中身を確認したamountを代入
$amount = get_post('amount');

// カート内の商品の個数を変更
if(update_cart_amount($db, $cart_id, $amount)){
  // trueの場合、成功メッセージ
  set_message('購入数を更新しました。');
} else {
  // falseの場合、エラーメッセージ
  set_error('購入数の更新に失敗しました。');
}

// カートページへ遷移(戻る)
redirect_to(CART_URL);