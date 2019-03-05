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
      <img style="display: block; margin-left: auto; margin-right: auto;"  src='<?php echo $order['barcode_url']; ?>'>
     <div style="text-align: center;font-size:20px;"> <?php echo $order['invoice_prefix']; ?></div>

      <tr>
        <td colspan='2'><bold style="border:2px black solid; width:20px;">集</bold>   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    <?php echo $order['shipping_zone']; ?>

        </td>
      </tr>
      <tr>
        <td colspan='2' style="min-height:100px"><bold style="border:2px black solid; width:20px;">收</bold>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <?php echo $order['shipping_address']['shipping_firstname']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <?php echo $order['shipping_phone']; ?>
        <div > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <?php echo $order['shipping_address']['shipping_address_1']; ?></div>
        </td>

      </tr>
        <tr >
          <td style="height:250px"><div >寄</div>
            <div >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AME&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 647-498-8891 </div>



            <img style="  display: block; margin-left: auto; margin-right: auto;" src='https://barcode.tec-it.com/barcode.ashx?data=<?php echo $order['delivery_number']; ?>&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0'>


            <div >


              <?php echo date('Y-m-d H:i:s');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 打印时间

            </div>
            <div  style="font-size:12px">快件送达收件地址，经收件人或寄件人允许的代收人签字，视为送达，您的签字代表您已经收到包裹，并已确认商品信息无损，包赚完好，没有
            划痕，破损表面质量问题。  签收栏：</div>

          </td>


        </tr>

        <tr>
          <td colspan='2' >        <img style="  display: block; margin-left: auto; margin-right: auto;" src='https://barcode.tec-it.com/barcode.ashx?data=<?php echo $order['delivery_number']; ?>&code=Code39FullASCII&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0'>

          </td>
      <tr>
        <td colspan='2' style="min-height:100px"><bold style="border:2px black solid; width:20px;">收</bold>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <?php echo $order['shipping_address']['shipping_firstname']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <?php echo $order['shipping_phone']; ?>
          <div > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  <?php echo $order['shipping_address']['shipping_address_1']; ?></div>
        </td>
        </tr>
        <tr><td colspan='2'><div >寄</div>
            <div  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AME &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 647-498-8891 </div>
         <div style="font-size:12px">
            商品：
        <?php foreach ($order['product'] as $product) { ?>
           <?php echo $product['name'] . $product['meta_title'] . " ( " . $product['mpn'] . " ) X " . $product['quantity']."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";        ?>
        <?php } ?>
         </div>

            <div style="font-size:12px">江门包裹局 验视人： 谢俊钊（快递清关场） &nbsp;&nbsp;&nbsp;已验视  </div>

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