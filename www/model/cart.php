<?php 
// 定数MODEL_PATH(modelディレクトリ)のfunctions.phpファイルを読み込み
require_once MODEL_PATH . 'functions.php';
// 定数MODEL_PATH(modelディレクトリ)のdb.phpファイルを読み込み
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  // user_idカートに入っている商品全て取得するSQL文
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";

  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id);
  // SQLを実行して結果をfetchAllで取得
  return fetch_all_query($db, $sql, $params);
}

function get_user_cart($db, $user_id, $item_id){
  // item_idでテーブルを結合、$user_idのカートに入っている$item_idの情報を取得するSQL文
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id, $item_id);
  // SQLを実行して結果をfetchで取得
  return fetch_query($db, $sql, $params);
}

function add_cart($db, $user_id, $item_id ) {
  // $user_idのカートに入っている$item_idの情報を取得する
  $cart = get_user_cart($db, $user_id, $item_id);
  // get_user_cartの返り値がfalseだった場合(選んだ商品が一つもカートに入っていない、SQL実行失敗)
  if($cart === false){
    // 新しく商品をカゴに投入する(関係ないエラーはどうするんだろう？)
    // ↑やっぱり厳密にはダメ(金融系とかエラーの種類を特定する必要があるような場合)
    return insert_cart($db, $user_id, $item_id);
  }
  // tureだった場合(すでに同じ商品があった場合)カートの中身を変更する(個数を１つ増やす)
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  // カートに商品を追加するSQL文
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($item_id, $user_id, $amount);
  // SQLを実行
  return execute_query($db, $sql, $params);
}

function update_cart_amount($db, $cart_id, $amount){
  // // カートに入っている商品の個数を変更
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($amount, $cart_id);
  // SQLを実行
  return execute_query($db, $sql, $params);
}

function delete_cart($db, $cart_id){
  // カートを削除するSQL文
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($cart_id);
  // sql実行
  return execute_query($db, $sql, $params);
}

function purchase_carts($db, $carts){
  // 購入するためにカートに商品が入っていること、公開ステータス、在庫数に問題ないかチェック
  if(validate_cart_purchase($carts) === false){
    // どれか一つでも問題があればfalseを返す
    return false;
  }
  foreach($carts as $cart){
    // 問題なければ在庫の更新
    if(update_item_stock(
        $db, 
        // 購入する商品ID
        $cart['item_id'], 
        // 在庫から購入する分を引いた数
        $cart['stock'] - $cart['amount']
      ) === false){
      // SQL実行を失敗した場合、エラーメッセージ
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  // カートの中身を削除する($carts[0]['user_id']？)
  // ↑カートの中身が何個か分からないから0を指定してる
  delete_user_carts($db, $carts[0]['user_id']);
}

function delete_user_carts($db, $user_id){
  // カート(の中身)を削除するSQL文
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id);
  // SQLを実行する
  execute_query($db, $sql, $params);
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    // 商品の値段×個数を合計していく
    $total_price += $cart['price'] * $cart['amount'];
  }
  // 合計値を返す
  return $total_price;
}

function validate_cart_purchase($carts){
  // カートの中身をカウント、空だった場合
  if(count($carts) === 0){
    // エラーメッセージ
    set_error('カートに商品が入っていません。');
    // 返り値としてfalseを返す
    return false;
  }
  // ifがfalseだった場合(カートに商品が一つ以上入っている)
  foreach($carts as $cart){
    // カートに入っている商品のステータスが1(公開)かどうか
    if(is_open($cart) === false){
      // ステータスが0(非公開)のものがある場合、エラーメッセージ
      set_error($cart['name'] . 'は現在購入できません。');
    }
    // カートに入ってる商品の在庫が0になっていないか
    if($cart['stock'] - $cart['amount'] < 0){
      // 在庫が0の場合、エラーメッセージ
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  // エラーの数をカウントする
  if(has_error() === true){
    // エラーが一つ以上ある場合、falseを返す
    return false;
  }
  // エラーが0の場合trueを返す
  return true;
}

function purchase_history($db, $user_id, $item_id, $amount){
// 購入履歴を登録するSQL文
  $sql = "
    INSERT INTO
      purchase_history(
        user_id,
        item_id,
        amount
      )
    VALUES(?, ?, ?)    
  ";
  // プレースホルダ用、executeに渡すので配列にする
  $params = array($user_id, $item_id, $amount);
  // SQLを実行
  return execute_query($db, $sql, $params);
}

function purchase_detail($db, $item_name, $att_price, $amount){
  // 購入明細を登録するSQL文
    $sql = "
      INSERT INTO
        purchase_detail(
          item_name,
          att_price,
          amount
        )
      VALUES(?, ?, ?)    
    ";
    // プレースホルダ用、executeに渡すので配列にする
    $params = array($item_name, $att_price, $amount);
    // SQLを実行
    return execute_query($db, $sql, $params);
  }