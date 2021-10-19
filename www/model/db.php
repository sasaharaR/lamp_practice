<?php
// MySQL用のDSN文字列(structured query language,構造化した問い合わせ言語)
function get_db_connect(){
  // $dsnに(data source name、DB接続に必要な情報)DB名、DBのホスト名(サーバー名)、文字コードをセット
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
  
  // データベースに接続
  try {
    // newでPDOクラス(コンストラクタ?,データベースを操作するもの)からインスタンス作成($dbh)
    // MySQLサーバーへの接続時に実行するコマンドを指定する。文字化けを防ぐ
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    // オプションを設定、エラーが起きた時にどう処理するか、例外をスローしてくれる
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // エスケープみたいなもの？(SQLインジェクション対策？)
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // フェッチのモードを設定(連想配列で取得する)
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // エラーが出たらPDOExceptionにスローされる  
  } catch (PDOException $e) {
    // エラーメッセージ
    exit('接続できませんでした。理由：'.$e->getMessage() );
    
  }
  // 戻り値を返す
  return $dbh;
}

function fetch_query($db, $sql, $params = array()){
  try{
    // SQL文の実行準備 
    $statement = $db->prepare($sql);
    // SQLを実行
    $statement->execute($params);
    // 結果をfetchで取得(一行だけだからfetch)
    return $statement->fetch();
  // 途中でエラーがあったらスロー
  }catch(PDOException $e){
    // エラーメッセージ
    set_error('データ取得に失敗しました。');
  }
  // 返り値にfalseを返す
  return false;
}

function fetch_all_query($db, $sql, $params = array()){
  try{
    // SQL文の実行準備
    $statement = $db->prepare($sql);
    // SQLを実行 
    $statement->execute($params);
    // 結果をfetchAllで取得(全部欲しいのでfetchAll)
    return $statement->fetchAll();
  // 途中でエラーがあればスロー 
  }catch(PDOException $e){
    // エラーメッセージ
    set_error('データ取得に失敗しました。');
  }
  // 返り値にfalseを返す
  return false;
}

function execute_query($db, $sql, $params = array()){
  try{
    // SQL文の実行準備
    $statement = $db->prepare($sql);
    // 返り値にSQL実行を返す
    return $statement->execute($params);
  // 途中でエラーならスロー
  }catch(PDOException $e){
    // エラーメッセージ
    set_error('更新に失敗しました。');
  }
  // エラーなら返り値にfalseを返す
  return false;
}