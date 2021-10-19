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
// ログインしていない場合($_SESSION変数が空の場合)
if(is_logined() === false){
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

// データベースに接続
$db = get_db_connect();
// データベースのユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// ユーザータイプが1(管理者)じゃない場合
if(is_admin($user) === false){
  // ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// issetで中身を確認したnameを代入
$name = get_post('name');
// issetで中身を確認したpriceを代入
$price = get_post('price');
// issetで中身を確認したstatusを代入
$status = get_post('status');
// issetで中身を確認したstockを代入
$stock = get_post('stock');

// issetで中身を確認したiamgeを代入
$image = get_file('image');

// 商品情報をデータベースに登録し画像をimageディレクトリに移動
if(regist_item($db, $name, $price, $stock, $status, $image)){
  // 問題がない場合、成功メッセージ
  set_message('商品を登録しました。');
}else {
  // そうでない場合、エラーメッセージ
  set_error('商品の登録に失敗しました。');
}

// 商品管理ページへ遷移
redirect_to(ADMIN_URL);