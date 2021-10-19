<?php

function dd($var){
  // デバッグ用？(dump＆dieの略、laravelでdump後 勝手に止まってくれるらしい)
  var_dump($var);
  // 変数の中身を表示する
  exit();
  // 実行停止
}

// 指定のURLに遷移させる
function redirect_to($url){
  // header関数で指定のURLへ遷移
  header('Location: ' . $url);
  // php処理が先に進まないようにexitで終了
  exit;
}

function get_get($name){ // (使われている？)
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

// POSTされた入力フォームの値がセットされているか確認
function get_post($name){
  // issetしたPOSTされた入力フォームの値がtrueかどうか
  if(isset($_POST[$name]) === true){
    // 結果がtrueの場合、戻り値(セットされた値)を返す 
    return $_POST[$name];
  };
  // 結果がfalseの場合、戻り値(空)を返す
  return '';
  
}

function get_file($name){
  // POSTでアップロードされたファイルの値がセットされているか確認 
  if(isset($_FILES[$name]) === true){
    // 結果がtrueの場合、戻り値(セットされた値)を返す
    return $_FILES[$name];
  };
  // 結果がfalseの場合、空の配列を返す
  return array();
}

// セッション情報があるか(残ってるか)
function get_session($name){
  // $_SESSION変数がセットされているか確認
  if(isset($_SESSION[$name]) === true){
    // 結果がtrueの場合、戻り値(セッション情報)を返す
    return $_SESSION[$name];
  };
  // 結果がfalseの場合、戻り値(空)を返す
  return '';
}

function set_session($name, $value){
  // $_SESSION[$name]に$valueを保存
  $_SESSION[$name] = $value;
}

function set_error($error){
  // $_SESSION[__errors][]に$error(エラーメッセージ)を代入
  $_SESSION['__errors'][] = $error;
  
}

function get_errors(){
  // $errorsにget_session('__errors')を代入
  $errors = get_session('__errors');
  // エラーメッセージがない場合
  if($errors === ''){
    // 返り値として空の配列を返す
    return array();
  }
  // エラーメッセージが格納されていた場合$_SESSION['__errors']に空配列を代入(値に影響を与えないためだと思う)
  set_session('__errors',  array());
  // 戻り値に$errorsを返す
  return $errors;
}

function has_error(){
  // エラーの数が0じゃない場合(isset($_SESSION['__errors'])は必要なのか？)
  // ↑中身のないものをカウントできないってエラーになっちゃうみたい(エラーがない場合)
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  // $_SESSION[__messages][]に$message(成功メッセージ)を代入
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  // $messagesにget_session('__messages')を代入
  $messages = get_session('__messages');
  // 成功メッセージがない場合
  if($messages === ''){
    // 返り値として空の配列を返す
    return array();
  }
  // 成功メッセージが格納されていた場合$_SESSION['__message']に空配列を代入(値に影響を与えないためだと思う)
  set_session('__messages',  array());
  // 戻り値に$messagesを返す
  return $messages;
}

// ログインしているかの確認
function is_logined(){
  // $_SESSION変数に保存されているユーザIDが空じゃないか確認する
  return get_session('user_id') !== '';
}

function get_upload_filename($file){
  // postされた物かどうか、拡張子のチェック
  if(is_valid_upload_image($file) === false){
    // エラーがあれば空の値を返す
    return '';
  }
  // exif_imagetypeで画像ファイルかどうか確認してる？(mimeタイプの確認)
  // ２回目？
  $mimetype = exif_imagetype($file['tmp_name']);
  // 画像タイプを取得して(定数ファイルが返り値らしい)、それがなんでPERMITTED_IMAGE_TYPESの配列に。。。？
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  // ランダムに取得した文字列に拡張子をつける
  return get_random_string() . '.' . $ext;
}

function get_random_string($length = 20){
  // substr(指定された文字を返す、substr(返す文字の最初の位置、返す文字の数))
  // base_convert(進数の変換)
  // hash(入れた値が適当な数値(厳密には違う)になる),Secure Hash Algorithm(256ビットの値を返す)
  // uniqid(現在時刻から唯一の値を作る)
  // uniqidが作った13文字がhashでハッシュ値にして返されて、base_convertで１６進数から36進数に変換されてsubstrで先頭文字から20文字まで(指定がなければ)を返す)
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

function save_image($image, $filename){
  // move_uploaded_fileを使って画像ファイルを移動(元のファイル名、移動先のファイル名)
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

function delete_image($filename){
  // file_existsでファイルが存在するか確認
  if(file_exists(IMAGE_DIR . $filename) === true){
    // ファイルがあればunlinkでファイルを削除
    unlink(IMAGE_DIR . $filename);
    // trueを返す
    return true;
  }
  // ファイルがなければfalseを返す
  return false;
  
}



function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  // 入力した文字数を取得、$lengthに代入
  $length = mb_strlen($string);
  // 返り値に、入力文字数が最小文字数以上かつ最大文字数以下である
  return ($minimum_length <= $length) && ($length <= $maximum_length);
  
}

function is_alphanumeric($string){
  // 入力された文字が半角の英数字である
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

function is_positive_integer($string){
  // 入力された文字が半角の整数である
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

function is_valid_format($string, $format){
  // 返り値、入力された文字が入力条件に一致している(preg_matchはマッチなら１を返す)
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  // is_uploaded_fileでpostでアップされたか調べる
  if(is_uploaded_file($image['tmp_name']) === false){
    // falseの場合、エラーメッセージ(厳密には文言おかしいと思う)
    set_error('ファイル形式が不正です。');
    // falseを返す
    return false;
  }
  // exif_imagetypeで画像ファイルかどうか確認してる(mimeタイプの確認)
  $mimetype = exif_imagetype($image['tmp_name']);
  // 画像の拡張子の確認(PERMITTED_IMAGE_TYPESに配列として入ってるJPEG,PNGと照合)
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    // jpg,png以外の形式だった場合、エラーメッセージ(jpegは自動的にjpgになるらしい)
    // 配列要素から文字列を作成する、implode(区切り文字, 配列)→『jpg,png』
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    // falseを返す
    return false;
  }
  // 拡張子に問題なければtrueを返す
  return true;
}

// エスケープ(XSSを回避するため)
function h($str){
  // htmlspecialchars→HTMLタグ(「&」、「<」、「>」)を文字列として扱う
  // ENT_QUOTES→「”」、「’」を文字列として扱う、UTF-8→文字コード
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

// トークンの生成、csrf対策
function get_csrf_token(){
  // uniqidが作った13文字がhashでハッシュ値にして返されて、base_convertで１６進数から36進数に変換されてsubstrで先頭文字から20文字まで(指定がなければ)を返す)
  // substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
  $token = get_random_string(30);
  // $_SESSON[csrt_token]にトークンを保存
  set_session('csrf_token', $token);
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  // トークンの中身確認
  if(isset($token) === '') {
    // トークンがない場合false
    return false;
  }
  // $_SESSIONに保存されたトークンと比較
  return $token === get_session('csrf_token');
}