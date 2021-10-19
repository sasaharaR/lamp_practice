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
// ログインチェックのためセッションスタート
session_start();
// ログインしていない($_SESSION変数が空)の場合
if(is_logined() === false){
  // ログイン画面に遷移
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
// 中身を確認(isset)したcart_idを代入
$cart_id = get_post('cart_id');
// カートを削除
if(delete_cart($db, $cart_id)){
  // trueなら成功メッセージ
  set_message('カートを削除しました。');
} else {
  //falseならエラーメッセージ
  set_error('カートの削除に失敗しました。');
}
// カートページに遷移(戻る)
redirect_to(CART_URL);