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
  <h1>
    <?php echo $text_invoice; ?> #<?php echo $order['order_id']; ?>
    <button style="float: right;" onclick="printer(<?php echo $order['order_id']; ?>)">Print</button>
  </h1>
  <div id="printer-area-<?php echo $order['order_id']; ?>" style="page-break-after: always;">
    <table class="table table-bordered">
      <thead>
      <tr>
        <td colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td style="width: 50%; vertical-align: initial;">
          <address>
            <strong><?php echo $order['store_name']; ?></strong><br />
            <?php echo $order['store_address']; ?>
          </address>
          <b><?php echo $text_telephone; ?></b> <?php echo $order['store_telephone']; ?><br />
          <?php if ($order['store_fax']) { ?>
          <b><?php echo $text_fax; ?></b> <?php echo $order['store_fax']; ?><br />
          <?php } ?>
          <b><?php echo $text_email; ?></b> <?php echo $order['store_email']; ?><br />
          <b><?php echo $text_website; ?></b> <?php echo $order['store_url']; ?>
        </td>
        <td style="width: 50%; vertical-align: initial;">
          <b><?php echo $text_order_id; ?></b> <?php echo $order['invoice_prefix']; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
          <b><?php echo $text_payment_method; ?></b> <?php echo $order['payment_method']; ?><br />
          <?php if ($order['shipping_method']) { ?>
          <b><?php echo $text_shipping_method; ?></b> <?php echo $order['shipping_method']; ?><br />
          <?php } ?>
          <?php if ($order['admin_name']) { ?>
          <b><?php echo $text_admin_name; ?></b> <?php echo $order['admin_name']; ?>
          <?php } ?>
        </td>
      </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
      <tr>
        <td style="width: 50%;"><b><?php echo $text_payment_address; ?></b></td>
        <td style="width: 50%;"><b><?php echo $text_shipping_address; ?></b></td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td style="vertical-align: initial;">
          <address style="margin-bottom: 0;"><?php echo $order['payment_address']; ?></address>
        </td>
        <td style="vertical-align: initial;">
          <address style="margin-bottom: 0;"><?php echo $order['shipping_address']; ?></address>
        </td>
      </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td><?php echo $entry_weight; ?></td>
        <td><?php echo $order['weight'];?></td>
      </tr>
      <tr>
        <td><?php echo $entry_category; ?></td>
        <td><?php echo $order['category_name']; ?></td>
      </tr>
      <tr>
        <td><?php echo $entry_barcode; ?></td>
        <td>
          <img src='<?php echo $order['barcode_url']; ?>'>
          <br>
          <span style="margin-left: 15px; font-size: 18px;"><?php echo $order['invoice_prefix'];?></span>
        </td>
      </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
      <tr>
        <td><b><?php echo $column_product; ?></b></td>
        <td><b><?php echo $column_model; ?></b></td>
        <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
        <td class="text-right"><b><?php echo $column_price; ?></b></td>
        <td class="text-right"><b><?php echo $column_total; ?></b></td>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($order['product'] as $product) { ?>
      <tr>
        <td><?php echo $product['name']; ?>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?></td>
        <td><?php echo $product['model']; ?></td>
        <td class="text-right"><?php echo $product['quantity']; ?></td>
        <td class="text-right"><?php echo $product['price']; ?></td>
        <td class="text-right"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($order['voucher'] as $voucher) { ?>
      <tr>
        <td><?php echo $voucher['description']; ?></td>
        <td></td>
        <td class="text-right">1</td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($order['total'] as $total) { ?>
      <tr>
        <td class="text-right" colspan="4"><b><?php echo $total['title']; ?></b></td>
        <td class="text-right"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
      <?php if ($order['comment']) { ?>
      <tr>
        <td><b><?php echo $text_comment; ?></b></td>
        <td colspan="4"><?php echo $order['comment']; ?></td>
      </tr>
      <?php } ?>
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