<?php
// ユーザー登録画面のphp

// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のdb.phpファイルを読み込み
require_once MODEL_PATH . 'db.php';


function get_user($db, $user_id){
  // データベースから登録IDを参照してユーザー情報を取得するSQL文
  // (1行だけ欲しいからLIMIT句を使う,のか？id参照してるからいらないのでは？)
  // ↑LIMITはあってもなくても大丈夫らしい(コード書いた人の癖？)
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id);
  // SQLを実行して結果をfetchで取得
  return fetch_query($db, $sql, $params);
}

function get_user_by_name($db, $name){
  // データベースからユーザIDを参照してユーザー情報を取得するSQL文
  // (1行だけ欲しいからLIMIT句を使う,のか？name参照してるからいらないのでは？)
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($name);
  // SQLを実行して結果をfetchで取得
  return fetch_query($db, $sql, $params);
}

function login_as($db, $name, $password){
  // $name(ユーザーID)を参照してSQL文を実行、ユーザー情報を取得→$userに代入
  $user = get_user_by_name($db, $name);
  // $userに返り値としてfalseが入っているか、取得したデータとパスワードが一致しない場合
  if($user === false || $user['password'] !== $password){
    // 返り値としてfalseを返す
    return false;
  }
  // ifがtrueだった場合、$_SESSION[user_id]に取得したuser_idを保存
  set_session('user_id', $user['user_id']);
  // 返り値としてセッション情報を保存した$userを返す
  return $user;
}

function get_login_user($db){
  // ＄＿SESSION['user_id']がセットされてるか確認して$login_user_idに代入
  $login_user_id = get_session('user_id');
  // データベースからユーザIDを参照してユーザー情報を取得する
  return get_user($db, $login_user_id);
}

function regist_user($db, $name, $password, $password_confirmation) {
  // 入力されたユーザIDまたはパスワードに問題がある場合(どちらか、もしくはどちらも返り値がfalse)
  // $is_valid_user_name && $is_valid_passwordの結果がfalse
  if(is_valid_user($name, $password, $password_confirmation) === false){
    // 返り値にfalseを返す
    return false;
  }
  // trueの場合SQLを実行してデータベースにユーザIDとパスワードを登録する
  return insert_user($db, $name, $password);
}

function is_admin($user){
  // ユーザータイプが1(管理者)の場合
  return $user['type'] === USER_TYPE_ADMIN;
}

function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。⇦？？？？？？(謎のまま)
  // 入力された文字が条件に合っていたか(trueかfalseが返ってる)を$is_valid_user_nameに代入
  $is_valid_user_name = is_valid_user_name($name);
  // 入力された文字が条件に合っていたか(trueかfalseが返ってる)を$is_valid_passwordに代入
  $is_valid_password = is_valid_password($password, $password_confirmation);
  // それぞれの返り値がtrueか確認
  return $is_valid_user_name && $is_valid_password ;
}

function is_valid_user_name($name) {
  // $is_validにtrueを代入
  $is_valid = true;
  // 入力された文字数が最小文字数６文字以下または最大文字数100文字以上の場合
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    // エラーメッセージ(最小文字数６文字、最大文字数100文字)
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    // $is_validにfalseを代入
    $is_valid = false;
  }
  // 入力された文字が半角の英数字以外だった場合
  if(is_alphanumeric($name) === false){
    // エラーメッセージ
    set_error('ユーザー名は半角英数字で入力してください。');
    // $is_validにfalseを代入
    $is_valid = false;
  }
  // 返り値として$is_validを返す
  return $is_valid;
}

function is_valid_password($password, $password_confirmation){
  // $is_validにtrueを代入
  $is_valid = true;
  // 入力された文字数が最小文字数６文字以下または最大文字数100文字以上の場合
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    // エラーメッセージ(最小文字数６文字、最大文字数100文字)
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    // $is_validにfalseを代入
    $is_valid = false;
  }
  // 入力された文字が半角の英数字以外だった場合
  if(is_alphanumeric($password) === false){
    // エラーメッセージ
    set_error('パスワードは半角英数字で入力してください。');
    // $is_validにfalseを代入
    $is_valid = false;
  }
  // 入力したパスワードと確認用に入力したパスワードが一致しない場合
  if($password !== $password_confirmation){
    // エラーメッセージ
    set_error('パスワードがパスワード(確認用)と一致しません。');
    // $is_validにfalseを代入
    $is_valid = false;
  }
  // 返り値として$is_validを返す
  return $is_valid;
  
}

function insert_user($db, $name, $password){
  // データベースのuserテーブルにユーザIDとパスワードを登録するSQL文
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (?, ?);
  ";
  // プレースホルダ用、executeに渡すので配列
  $params =array($name, $password);
  // 返り値にSQLの準備→実行を返す
  return execute_query($db, $sql, $params);
}

// 選択した履歴を取得
function get_history($db, $history_id){
  // 合計金額を取得するために購入明細のテーブルと結合(SUMを使う)
  $sql = "
  SELECT
    purchase_history.history_id,
    purchase_history.create_datetime,
    SUM(purchase_detail.att_price * purchase_detail.amount) AS total_price
  FROM
    purchase_history
  JOIN
    purchase_detail
  ON
    purchase_history.history_id = purchase_detail.history_id
  WHERE
    purchase_history.history_id = ?
  GROUP BY
    purchase_history.history_id  
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($history_id);
  // SQLを実行して結果をfetchで取得
  return fetch_query($db, $sql, $params);
}

// 通常ユーザー用
function get_histories($db, $user_id){
  // 合計金額を取得するために購入明細のテーブルと結合(SUMを使う)
  // 注文番号でグループ化してDESCで日時が新しい順に取得
  $sql = "
    SELECT
      purchase_history.history_id,
      purchase_history.create_datetime,
      SUM(purchase_detail.att_price * purchase_detail.amount) AS total_price
    FROM
      purchase_history
    JOIN
      purchase_detail
    ON
      purchase_history.history_id = purchase_detail.history_id
    WHERE
      user_id = ?
    GROUP BY
      history_id
    ORDER BY
      create_datetime desc 
    ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id);
  // SQLを実行して結果をfetchで取得
  return fetch_all_query($db, $sql, $params);
}

// 管理者用
function get_history_admin($db){
  // 合計金額を取得するために購入明細のテーブルと結合(SUMを使う)
  // 注文番号でグループ化してDESCで日時が新しい順に取得
  $sql = "
    SELECT
      purchase_history.history_id,
      purchase_history.create_datetime,
      SUM(purchase_detail.att_price * purchase_detail.amount) AS total_price
    FROM
      purchase_history
    JOIN
      purchase_detail
    ON
      purchase_history.history_id = purchase_detail.history_id
    GROUP BY
      history_id
    ORDER BY
      create_datetime desc 
    ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array();
  // SQLを実行して結果をfetchで取得
  return fetch_all_query($db, $sql, $params);
}

// 購入明細
function get_detail($db, $history_id){
  $sql = "
    SELECT
      purchase_detail.att_price,
      purchase_detail.amount,
      purchase_detail.create_datetime,
      SUM(purchase_detail.att_price * purchase_detail.amount) AS total_price,
      items.name
    FROM
      purchase_detail
    JOIN
      items
    ON
      purchase_detail.item_id = items.item_id
    WHERE
      history_id = ?
    GROUP BY
      purchase_detail.att_price,
      purchase_detail.amount,
      purchase_detail.create_datetime,
      items.name
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($history_id);
  // SQLを実行して結果をfetchで取得
  return fetch_all_query($db, $sql, $params);
}