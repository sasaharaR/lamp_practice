<?php
// ログインの処理のphp(login_view.phpからのpostを受ける)
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';

// ログインチェックのためセッションスタート
session_start();

// ログインしている($_SESSION変数のユーザIDが空じゃない)場合
if(is_logined() === true){
  // すでにログインしているのでHOME_URL(index.phpファイル,商品一覧ページ)へ遷移
  redirect_to(HOME_URL);
}

// $nameにisset済みのPOST'name'を代入
$name = get_post('name');
// $passwordにisset済みのPOST'password'を代入
$password = get_post('password');

// データベースに接続
$db = get_db_connect();


// $user = login_asの返り値(ログイン状態かどうか)を代入
$user = login_as($db, $name, $password);
// データ取得に失敗してるかパスワードが一致しない場合
if( $user === false){
  //　エラーメッセージ
  set_error('ログインに失敗しました。');
  // ログイン画面(login.php)に遷移(やり直し)
  redirect_to(LOGIN_URL);
}
// ifがtrueだった場合
// 成功メッセージ
set_message('ログインしました。');
// ログインしたユーザーのタイプが１(管理者)だった場合
if ($user['type'] === USER_TYPE_ADMIN){
  // 商品管理ページ(admin.php)に遷移
  redirect_to(ADMIN_URL);
}
// HOME_URL(index.phpファイル,商品一覧ページ)へ遷移
redirect_to(HOME_URL);
