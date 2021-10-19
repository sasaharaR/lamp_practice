<?php
// 新規ユーザー登録の処理のphp(signup_view.phpからのpostを受ける)

// const/php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のuser.phpファイルを読み込み
require_once MODEL_PATH . 'user.php';

// ログインチェックのためセッションスタート
session_start();

// ログインしている($_SESSION変数のユーザIDが空じゃない)場合
if(is_logined() === true){
  // HOME_URL(index.phpファイル,商品一覧ページ)へ遷移
  redirect_to(HOME_URL);
}

// $nameにisset済みのPOST'name'を代入
$name = get_post('name');
// $passwordにisset済みのPOST'password'を代入
$password = get_post('password');
// $password_confirmationにisset済みのPOST'password_confirmation'を代入
$password_confirmation = get_post('password_confirmation');

// データベースに接続
$db = get_db_connect();


try{
  // $resultにregist_userの返り値(tureかfalse)を代入
  $result = regist_user($db, $name, $password, $password_confirmation);
  // falseだった場合
  if( $result=== false){
    // エラーメッセージをセット
    set_error('ユーザー登録に失敗しました。');
    // 新規ユーザー登録画面(signup.php)に遷移(やり直し)
    redirect_to(SIGNUP_URL);
    
  }
//　途中でエラーがあればスロー  
}catch(PDOException $e){
  // エラーメッセージをセット
  set_error('ユーザー登録に失敗しました。');
  // 新規ユーザー登録画面(signup.php)に遷移(やり直し)
  redirect_to(SIGNUP_URL);
}
// ifの結果がfalse(登録に問題ない)だった場合、成功メッセージをセット
set_message('ユーザー登録が完了しました。');
// ログイン状態にする($_SESSIONにユーザー情報を保存)
login_as($db, $name, $password);
// HOME_URL(index.phpファイル,商品一覧ページ)へ遷移
redirect_to(HOME_URL);