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

// ログインしていない($_SESSION変数が空）場合
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

// データベースからユーザIDを参照してユーザー情報を取得
$user = get_login_user($db);

// ユーザータイプが１(管理者)じゃなかった場合
if(is_admin($user) === false){
  // ログインページへ遷移
  redirect_to(LOGIN_URL);
}

// issetで中身を確認したitem_idを代入
$item_id = get_post('item_id');
// issetで中身を確認したchanges_toを代入
$changes_to = get_post('changes_to');

// 送信された値が'open(公開)だった場合
if($changes_to === 'open'){
  // ステータスを公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  // 成功メッセージ
  set_message('ステータスを変更しました。');
// 送信された値がclose(非公開)だった場合 
}else if($changes_to === 'close'){
  // ステータスを非公開に変更
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  // 成功メッセージ
  set_message('ステータスを変更しました。');
}else {
  // どちらでもない場合警告メッセージ
  set_error('不正なリクエストです。');
}

// 商品管理ページへ遷移
redirect_to(ADMIN_URL);