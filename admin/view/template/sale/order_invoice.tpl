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
    <table class="table table-bordered" style="font-size: 11px;">
      <thead>
      <tr>
        <td colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <div style="width: 50%; display: inline-block; float: left; text-align:center">
          <img src="http://www.americoexpress.com/ame/App/Home/View/Default/img/web/about_us_logo.png" style="width: 308px;">
        </div>
        <div style="width: 50%; display: inline-block; margin-top:18px; font-size:20px;">
          <b><?php echo $text_order_id; ?></b> <?php echo $order['invoice_prefix']; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
          <?php if ($order['admin_name']) { ?>
          <b><?php echo $text_admin_name; ?></b> <?php echo $order['admin_name']; ?>
          <?php } ?>
        </div>
        <td style="width: 50%; vertical-align: initial;">
          <address>
            <strong><?php echo $order['store_name']; ?></strong><br />
            <?php echo $order['store_address']; ?>
          </address>
        </td>
        <td style="width: 50%; vertical-align: initial;">
          <b><?php echo $text_telephone; ?></b> <?php echo $order['store_telephone']; ?><br />
          <?php if ($order['store_fax']) { ?>
          <b><?php echo $text_fax; ?></b> <?php echo $order['store_fax']; ?><br />
          <?php } ?>
          <b><?php echo $text_email; ?></b> <?php echo $order['store_email']; ?><br />
          <b><?php echo $text_search_website; ?></b> <?php echo $text_search_url; ?>
          <!--<b><?php echo $text_website; ?></b> <?php echo $order['store_url']; ?>
             -->
        </td>
      </tr>
      </tbody>
    </table>
    <table class="table table-bordered" style="font-size: 11px;">
      <thead>
      <tr>
        <td style="width: 50%;"><b><?php echo $text_payment_address; ?></b></td>
        <td style="width: 50%;"><b><?php echo $text_shipping_address; ?></b></td>
      </tr>
      </thead>
      <tbody>
      <tr>
        <td style="vertical-align: initial;">
          <b>
         <?php echo $order['payment_address']; ?>
          </b>
        </td>
        <td style="vertical-align: initial;">
          <b>
        <?php echo $order['shipping_address']; ?>
          </b>
        </td>
      </tr>
      </tbody>
    </table>
    <!--
    <table class="table table-bordered" style="font-size: 11px;">
      <tbody>

      <tr>
        <td><?php echo $entry_barcode; ?></td>
        <td  colspan="3">
          <img  src='<?php echo $order['barcode_url']; ?>'>
          <br>
          <span style="margin-left: 200px; "><?php echo $order['invoice_prefix'];?></span>
        </td>
      </tr>
      </tbody>
    </table>
    -->

    <table class="table table-bordered" style="font-size: 11px;">
      <thead>
      <tr>
        <td><b><?php echo $column_product; ?></b></td>
        <td><b><?php echo $column_spec; ?></b></td>
        <td><b><?php echo $column_upc; ?></b></td>
        <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
        <td class="text-right"><b><?php echo $column_price; ?></b></td>

        <!--
        <td class="text-right"><b><?php echo $column_total; ?></b></td>

        -->
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
        <td><?php echo $product['mpn']; ?></td>
        <td><?php echo $product['upc']; ?></td>
        <td class="text-right"><?php echo $product['quantity']; ?></td>
        <td class="text-right"><?php echo $product['price']; ?></td>
        <!--
        <td class="text-right"><?php echo $product['total']; ?></td>
        -->
      </tr>
      <?php } ?>
      <?php foreach ($order['voucher'] as $voucher) { ?>
      <tr>
        <td><?php echo $voucher['description']; ?></td>
        <td></td>
        <td class="text-right">1</td>
        <td class="text-right">11<?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>

      <?php } ?>
      <!--
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
      -->
      </tbody>
    </table>

    <table class="table table-bordered" style="font-size: 13px;">
      <thead>
      <tr>
        <td style="width: 25%;"><?php echo $entry_category; ?></td>
        <td style="width: 25%;"><?php echo $order['category_name']; ?></td>
        <td style="width: 25%;"><?php echo $entry_weight; ?></td>
        <td style="width: 25%;"><?php echo $order['weight'];?></td>
      </tr>
      </thead>
    </table>
    <h4><?php echo $text_signature; ?><u> &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;  </u></h4>
    <p>1.根據中國有關規定,海外包裹快遞進入中國大陸需要提供收件人身份證正反兩面複印件。<br>2.托運物品按實際裝箱情況填寫，我公司對任何因為包裹清單填寫错误造成的延遲清關，退關等不負責任。<br>
      3.根據中國海關與本國航空要求，我公司有權對快件開包檢查，對於中國海關要求包裹徵稅，我公司會通知寄件人<br>支付該部分稅款，並会在收到寄件人稅款後才予以清關。一切以中國海關核計為準以及做最後決定。<br>4.由於海關清關的不確定性，我公司不對包裹抵達收件人手裡的時間做任何承諾，一般7-10天送達。<br>
      5.如未購買保險，本包裹丟失賠償上限為100加元整。<br>6.保證上述向海關申報內容真實性，如出現虛假申報，一切後果需本人承擔。<br>
    </p>
    <div id="qrcode"  style="border-radius:5px;float: right; top:-15px;"><img src="http://chart.googleapis.com/chart?chs=260x260&cht=qr&chl=<?php $url="http://"."www.superpolarbear.com/index.php?route=information/tracking&ame_no=".$order['invoice_prefix']; echo urlencode($url); ?>" alt="<?php echo $manufacturer; ?> <?php echo $heading_title; ?> QR Code" width="130" height="130" title=" QR Code" /></div>
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