<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
  <meta charset="UTF-8" />
  <title><?php echo $title; ?></title>
  <base href="<?php echo $base; ?>" />
  <link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
  <script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
  <script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
  <link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
  <link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
  <script src="view/javascript/jquery/jqprint/jquery-migrate-1.1.0.js"></script>
  <script src="view/javascript/jquery/jqprint/jquery.jqprint.js"></script>
</head>
<body>
<div class="container">
  <?php foreach ($orders as $order) { ?>
  <h1><button onclick="printer(<?php echo $order['order_id']; ?>)">Print</button></h1>
  <div id="printer-area-<?php echo $order['order_id']; ?>" style="page-break-after: always;">

    <table class="table table-bordered">
      <tbody>
        <tr>
          <td><?php echo $order['invoice_prefix'];?></td>
          <td><img src='<?php echo $order['barcode_url']; ?>'></td>
        </tr>
        <tr>
          <td colspan='2'>收件人： <?php echo $order['shipping_address']['shipping_firstname']; ?></td>
        </tr>
        <tr>
          <td colspan='2'>收件地址： <?php echo $order['shipping_address']['shipping_address_1'] . " " . $order['shipping_address']['shipping_city'] . " " . $order['shipping_address']['shipping_zone'] . " " . $order['shipping_address']['shipping_country'] . " " . $order['shipping_address']['shipping_postcode']; ?></td>
        </tr>
        <tr><td colspan='2'>商品：
        <?php foreach ($order['product'] as $product) { ?>
           <?php echo $product['name'] . $product['meta_title'] . " ( " . $product['mpn'] . " ) X " . $product['quantity']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";        ?>
        <?php } ?>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
  <?php } ?>
</div>

<script>
    // 打印
    function printer(id) {
        $("#printer-area-" + id).jqprint();
    }
</script>
</body>
</html>