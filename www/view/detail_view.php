<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入明細</title>
    <!-- 購入明細のCSSを作る?　<link rel="stylesheet" href="<//?php print (STYLESHEET_PATH . 'history.css'); ?>"> -->
</head>
<body>
    <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

    <div class="container">
        <h1>購入明細</h1>
        <?php include VIEW_PATH . 'templates/messages.php'; ?>


    <table class="table table-bordered text-center">
        <thead class="thead-light">
            <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php print h($history['history_id']); ?></td>
                <td><?php print h($history['create_datetime']); ?></td>
                <td><?php print h($history['total_price']); ?>円</td>
            </tr>
        </tbody>
    </table>
      
    <table class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
        <th>商品名</th>
        <th>価格</th>
        <th>購入数</th>
        <th>小計</th>
        </tr>
    </thead>
    <tbody>
          <?php foreach($detail as $det){ ?>
          <tr>
            <td><?php print h($det['name']); ?></td>
            <td><?php print h($det['att_price']); ?>円</td>
            <td><?php print h($det['amount']); ?>個</td>
            <td><?php print h($det['total_price']); ?>円</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
</body>
</html>
