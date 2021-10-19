<?php
// 新規ユーザー登録画面のphp

// (requireはエラーが出た時に処理をやめてくれる)
// const.php(定数ファイル)を読み込み
require_once '../conf/const.php';
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';

// ログインチェックのためセッション開始
session_start();

// ログインしている($_SESSION変数のユーザIDが空じゃない)場合
if(is_logined() === true){
  // HOME_URL(index.phpファイル,商品一覧ページ)へ遷移
  redirect_to(HOME_URL);
  
}

// (includeはエラーが出た時に警告を出す、画面自体が表示されなくなることはない)
// 定数VIEW_PATH(viewディレクトリ)のsignup_view.phpファイル読み込み
include_once VIEW_PATH . 'signup_view.php';

