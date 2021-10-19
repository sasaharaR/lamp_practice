<?php
// ログアウトページのphp
// const.php(定数ファイル)を読み込み 
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// ログインチェックのためセッションスタート
session_start();

// $_SESSIONを全て削除(空の値を入れる)
$_SESSION = array();
// Cookieに保存されているセッションIDを削除
// セッション関連の設定を取得
$params = session_get_cookie_params();
// セッションに利用しているクッキーの有効期限を過去に設定することで無効化
setcookie(session_name(), '', time() - 42000,

  // クッキーを保存するパス
  $params["path"], 
  // クッキーが有効なドメイン
  $params["domain"],
  // クッキーのセキュア(https通信の時だけ送り返してみたいな設定？)
  $params["secure"], 
  // JavaScriptからアクセスできなくなるらしい
  $params["httponly"]
);
//　セッションIDを無効化
session_destroy();

// ログイン画面(login.php)に遷移
redirect_to(LOGIN_URL);

