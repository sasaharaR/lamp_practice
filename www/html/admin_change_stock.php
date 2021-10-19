<?php
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATHのfunction.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATHのuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';
// 定数MODEL_PATHのitem.phpファイルを読み込み
require_once MODEL_PATH . 'item.php';

// ログインチェックのためセッションスタート
session_start();

// ログインしていない状態($_SESSION変数が空)の場合
if(is_logined() === false){
  // ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// データベースに接続
$db = get_db_connect();

// データベースからユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// ユーザータイプが1(管理者)じゃない場合
if(is_admin($user) === false){
  // ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// トークンの確認
if(is_valid_csrf_token($_POST['csrf_token']) === false){
  // エラーメッセージ
  set_error('不正な操作です。');
  // カートページに遷移(戻る)
  redirect_to(ADMIN_URL);
} else {
    // トークンを作り直し$_SESSION変数に保存
    $token = get_csrf_token();
}


// issetで中身を確認したitem_idを代入
$item_id = get_post('item_id');
// issetで中身を確認したstockを代入
$stock = get_post('stock');

// 在庫数を変更する
if(update_item_stock($db, $item_id, $stock)){
  // 成功メッセージ
  set_message('在庫数を変更しました。');
} else {
  // 失敗すればエラーメッセージ
  set_error('在庫数の変更に失敗しました。');
}

// 商品管理ページへ遷移
redirect_to(ADMIN_URL);