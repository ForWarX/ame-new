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
</head>
<body>
<div class="container">
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always;">
    <?php foreach ($order['product'] as $product) { ?>
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
        <tr>
          <td colspan='2'>商品： <?php echo $product['name'] . $product['meta_title'] . " ( " . $product['mpn'] . " ) X " . $product['quantity']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>
  </div>
  <?php } ?>
</div>
</body>
</html>