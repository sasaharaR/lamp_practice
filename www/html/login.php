<?php
// ログインページのphp

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
// 定数VIEW_PATH(viewディレクトリ)のlogin_view.phpファイル読み込み
include_once VIEW_PATH . 'login_view.php';
