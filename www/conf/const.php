<?php
// ドキュメントルートは/var/www/html(ページを公開するために設定された場所)
//　 modelもviewも違うディレクトリなので$_SERVER[DOCUMENT_ROOT](ディレクトリを指定するのに使う)でドキュメントルートを取得、ディレクトリの場所を指定
// $_SERVERでmodelディレクトリのルートパスを取得して定数MODEL_PATHを設定
define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/');
// $_SERVERでviewディレクトリのルートパスを取得して定数VIEW_PATHを設定
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/');

// 画像ディレクトリのパスを定数IMAGE_PATHに設定(ルートパス、scrだから)
define('IMAGE_PATH', '/assets/images/');
// CSSディレクトリを定数STYLESHEET_PATHに設定(ルートパス、scrだから)
define('STYLESHEET_PATH', '/assets/css/');
// 画像ディレクトリのパスを定数IMAGE_PATHに設定
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' );

// データベース接続用
// データベースのホスト(サーバー)名を設定
define('DB_HOST', 'mysql');
// データベース名を設定
define('DB_NAME', 'sample');
// ユーザーIDを設定
define('DB_USER', 'testuser');
// パスワードを設定
define('DB_PASS', 'password');
// 文字コードを設定(文字化けを防ぐ)
define('DB_CHARSET', 'utf8');

// 新規ユーザー登録画面のパスを定数SIGNUP_URLに設定
define('SIGNUP_URL', '/signup.php');
// ログインページのパスを定数LOGIN_URLに設定
define('LOGIN_URL', '/login.php');
// ヘッダーのログアウトのリンクパスを定数LOGOUT_URLに設定
define('LOGOUT_URL', '/logout.php');
// 商品一覧ページのパスを定数LOGOUT_URLに設定
define('HOME_URL', '/index.php');
// カートページのパスを定数CART_URLに設定
define('CART_URL', '/cart.php');
// 購入完了画面のパスを定数CART_URLに設定
define('FINISH_URL', '/finish.php');
// 商品管理ページのパスを定数ADMIN_URLに設定
define('ADMIN_URL', '/admin.php');

// 半角の英数字のみ
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
// 半角の整数のみ
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');


// 登録の際のユーザIDの最小文字数
define('USER_NAME_LENGTH_MIN', 6);
// 登録の際のユーザIDの最大文字数
define('USER_NAME_LENGTH_MAX', 100);
// 登録の際のパスワードの最小文字数
define('USER_PASSWORD_LENGTH_MIN', 6);
// 登録の際のパスワードの最大文字数
define('USER_PASSWORD_LENGTH_MAX', 100);

// ユーザーのタイプが１の場合(管理者)
define('USER_TYPE_ADMIN', 1);
// ユーザーのタイプが２の場合(通常利用者)
define('USER_TYPE_NORMAL', 2);

// 新規登録商品名の最小文字数
define('ITEM_NAME_LENGTH_MIN', 1);
// 新規登録商品の最大文字数
define('ITEM_NAME_LENGTH_MAX', 100);

// 商品のステータスが公開
define('ITEM_STATUS_OPEN', 1);
// 商品のステータスが非公開
define('ITEM_STATUS_CLOSE', 0);

// 商品のステータス
define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));

// アップロードできる画像の拡張子
define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));