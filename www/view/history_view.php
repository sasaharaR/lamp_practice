<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include VIEW_PATH . 'templates/head.php'; ?>
    <title>購入履歴</title>
    <!-- 購入履歴用のCSSを作る?　<link rel="stylesheet" href="<//?php print (STYLESHEET_PATH . 'history.css'); ?>"> -->
</head>
<body>
    <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

    <div class="container">
        <h1>購入履歴</h1>
        <?php include VIEW_PATH . 'templates/messages.php'; ?>


<?php if(count($history) > 0){ ?>
    <table class="table table-bordered text-center">
    <thead class="thead-light">
        <tr>
        <th>注文番号</th>
        <th>購入日時</th>
        <th>合計金額</th>
        </tr>
    </thead>
    <tbody>
          <?php foreach($history as $his){ ?>
          <tr>
            <td><?php print h($his['history_id']); ?></td>
            <td><?php print h($his['create_datetime']); ?></td>
            <td><?php print h($his['total_price']); ?>円</td>
            <td>
            <form action="detail.php" method="post">
                <input type="submit" value="購入明細表示" class="btn btn-primary btn-block">
                <input type="hidden" name="history_id" value="<?php print h($his['history_id']); ?>">
                <input type="hidden" name="csrf_token" value="<?php print h($token); ?>">
            </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php } else { ?>
    <p class="text-danger">購入履歴はありません</p>
<?php } ?>
</body>
</html>
