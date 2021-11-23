<?php
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';

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
// データベースからユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// user_idの履歴を取得
// ログインしたユーザーのタイプが１(管理者)だった場合
if ($user['type'] === USER_TYPE_ADMIN){
  // 全ての購入履歴を取得
  $history = get_history_admin($db);
} else {
// そうじゃない場合ユーザーの購入履歴を取得
  $history = get_histories($db, $user['user_id']);
}

// 定数VIEW_PATH(viewディレクトリ)のhistory_view.phpファイル読み込み 
include_once VIEW_PATH . 'history_view.php';