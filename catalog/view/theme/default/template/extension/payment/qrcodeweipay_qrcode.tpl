<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
  <div align="center" id="qrcode">
    <p ><?php echo $qrcode_title ?></p>
  </div>
    <script src="catalog/view/javascript/qrcode.js"></script>
  <script>
    var url = "<?php echo $code_url;?>";
    //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
    var qr = qrcode(10, 'M');
    qr.addData(url);
    qr.make();
    var dom=document.createElement('DIV');
    dom.innerHTML = qr.createImgTag();
    var element=document.getElementById("qrcode");
    element.appendChild(dom);
  </script>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script>
function showTime() {
  var loop=true;
  $.ajax({
      url: 'index.php?route=extension/payment/qrcodeweipay_qrcode/isOrderPaied&order_id=<?php echo $order_id ?>',
      dataType: 'json',
      success: function(json) {
          if (json['result']) {
            loop = false;
            location.href = "<?php echo $action_success; ?>";
          } 
          if (loop) {
            setTimeout("showTime()", 1000);
          }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        //alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
  });
}

showTime();
</script>
<?php echo $footer; ?>
