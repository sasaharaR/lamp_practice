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

// トークンの確認
if(is_valid_csrf_token($_POST['csrf_token']) === false){
  // エラーメッセージ
  set_error('不正な操作です。');
  // カートページに遷移(戻る)
  redirect_to(HOME_URL);
} else {
    // トークンを作り直し$_SESSION変数に保存
    $token = get_csrf_token();
}

// データベースに接続
$db = get_db_connect();
// データベースからユーザIDを参照してユーザー情報を取得する
$user = get_login_user($db);

// POSTされてきたitem_idの中身を確認
$item_id = get_post('item_id');

// trueならカートに商品を追加する
if(add_cart($db,$user['user_id'], $item_id)){
  // 成功メッセージ
  set_message('カートに商品を追加しました。');
} else {
  // エラーメッセージ
  set_error('カートの更新に失敗しました。');
}

// 商品一覧ページ(index.php)に遷移
redirect_to(HOME_URL);