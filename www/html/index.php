<?php
// 商品一覧ページ
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';
// 定数MODEL_PATH(modelディレクトリ)のitem.phpファイルを読み込み
require_once MODEL_PATH . 'item.php';

// ログインチェックのためセッション開始
session_start();

// ログインしていない($_SESSION変数のユーザIDが空の)場合
if(is_logined() === false){
  // ログイン画面(login.php)に遷移
  redirect_to(LOGIN_URL);
}

// トークンを作成$_SESSION変数に保存
$token = get_csrf_token();

// データベースに接続
$db = get_db_connect();
// データベースからユーザIDを参照してユーザー情報を取得する
$user = get_login_user($db);
// データベースからステータスを参照して公開されている商品情報を取得する
$items = get_open_items($db);
// 定数VIEW_PATH(viewディレクトリ)のindex_view.phpファイル読み込み
include_once VIEW_PATH . 'index_view.php';