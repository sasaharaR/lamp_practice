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
// ログインしていない($_SESSION変数が空)場合
if(is_logined() === false){
  //ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// トークンを作成$_SESSION変数に保存
$token = get_csrf_token();

// データベースに接続
$db = get_db_connect();
// データベースからユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// ユーザータイプが1(管理者)じゃない場合
if(is_admin($user) === false){
  // ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// データベースから商品情報を全て所得する
$items = get_all_items($db);
// 定数VIEW_PATHのadmin_viewファイルを読み込み
include_once VIEW_PATH . '/admin_view.php';
