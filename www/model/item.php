<?php

// 商品ページのphp
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のdb.phpファイルを読み込み
require_once MODEL_PATH . 'db.php';


// DB利用

function get_item($db, $item_id){
  // item_idを参照してデータベースから商品情報を所得するSQL文
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $parama = array($item_id);
  // SQLを実行して結果をfetchで取得
  return fetch_query($db, $sql, $params);
  
}

// 全て商品情報を取得するSQL文
function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  // is_openの引数がtrueだった場合(デフォルト引数がfalse)
  if($is_open === true){
    // ステータスが１(公開)の商品情報を取得するSQL文になる
    $sql .= '
      WHERE status = 1
    ';
    
  }
  // 返り値としてfetchAllで結果を取得して返す
  return fetch_all_query($db, $sql);
  
}

function get_all_items($db){
  // データベースから全て商品情報を所得する
  return get_items($db);
}

function get_open_items($db){
  // ステータスが１(公開)の商品情報を返す
  return get_items($db, true);
}

function regist_item($db, $name, $price, $stock, $status, $image){
  // get_upload_filenameで取得したランダムな名前を代入
  $filename = get_upload_filename($image);
  // 送信されたそれぞれの値に問題があった場合
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    // falseを代入
    return false;
  }
  // trueだった場合、商品情報をデータベースに登録し画像をimageディレクトリに移動
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  // トランザクション開始
  $db->beginTransaction();
  // 商品情報をデータベースに登録
  if(insert_item($db, $name, $price, $stock, $filename, $status)
    // 画像をimagesディレクトリに移動
    && save_image($image, $filename)){
    // 上記二つの操作が成功した場合
    $db->commit();
    // trueを返す
    return true;
  }
  // 失敗した場合ロールバック
  $db->rollback();
  // falseを返す
  return false;
  
}

function insert_item($db, $name, $price, $stock, $filename, $status){
  // 送信された公開ステータスを代入
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  // itemsテーブルに商品情報を登録
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(?, ?, ?, ?, ?);
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($name, $price, $stock, $filename, $status_value);
  // SQL実行
  return execute_query($db, $sql, $params);
}

function update_item_status($db, $item_id, $status){
  // 商品のステータスを変更するSQL文
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($status, $item_id);
  // SQL実行
  return execute_query($db, $sql, $params);
}

function update_item_stock($db, $item_id, $stock){
  // 在庫数を更新するSQL文
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  // プレースホルダー用、executeに渡すために配列にする
  $params = array($stock, $item_id);
  // SQLの実行
  return execute_query($db, $sql, $params);
}

function destroy_item($db, $item_id){
  // データベースから商品情報を取得して代入
  $item = get_item($db, $item_id);
  // 取得できなかった場合
  if($item === false){
    // falseを返す
    return false;
  }
  // 取得に成功したらトランザクション開始
  $db->beginTransaction();
  // itemsテーブルから商品情報を削除する
  if(delete_item($db, $item['item_id'])
    // 画像ファイルを削除
    && delete_image($item['image'])){
    // 上記二つが成功した場合
    $db->commit();
    // tureを返す
    return true;
  }
  // そうでない場合ロールバック
  $db->rollback();
  // falseを返す
  return false;
}

function delete_item($db, $item_id){
  // itemsテーブルから商品情報を削除するSQL文
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($item_id);
  // SQL実行
  return execute_query($db, $sql, $params);
}


// 非DB

function is_open($item){
  // 返り値として$itemのステータスが１か比較
  return $item['status'] === 1;
}

function validate_item($name, $price, $stock, $filename, $status){
  // 送信した情報に問題がないか確認、それぞれに代入
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  // 全ての値がtrueか確認するため
  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

function is_valid_item_name($name){
  // trueを代入
  $is_valid = true;
  // 商品名($name)が’指定された最小文字数以上かつ最大文字数以下’でない場合
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    // エラーメッセージ
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    // falseを代入
    $is_valid = false;
  }
  // is_validを返す
  return $is_valid;
}

function is_valid_item_price($price){
  // trueを代入
  $is_valid = true;
  // 半角の整数じゃない場合
  if(is_positive_integer($price) === false){
    // エラーメッセージ
    set_error('価格は0以上の整数で入力してください。');
    // falseを代入
    $is_valid = false;
  }
  // is_validを返す
  return $is_valid;
}

function is_valid_item_stock($stock){
  // trueを代入
  $is_valid = true;
  // 半角の整数じゃない場合
  if(is_positive_integer($stock) === false){
    // エラーメッセージ
    set_error('在庫数は0以上の整数で入力してください。');
    // falseを代入
    $is_valid = false;
  }
  // is_validを返す
  return $is_valid;
}

function is_valid_item_filename($filename){
  // trueを代入
  $is_valid = true;
  // 画像($filename)が空の場合
  if($filename === ''){
    // falseを代入
    $is_valid = false;
  }
  // is_validを返す
  return $is_valid;
}

function is_valid_item_status($status){
  // trueを代入
  $is_valid = true;
  // 公開ステータスが選択されていない場合
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    // falseを代入
    $is_valid = false;
  }
  // is_validを返す
  return $is_valid;
}