<?php echo $header; ?>
<div id="common-success" class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <div class="text-center">
        <div id="qrcode">
        </div>
        <div class="alert alert-info" style="width: 256px; margin: 5px auto;"><?php echo $text_qrcode_description; ?></div>

      </div>
      <div>
        <img id="ott-qrcode-img" src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=<?php echo $code_url; ?>&choe=UTF-8" data-oid="<?php echo $order_id; ?>" title="Link to Google.com" style="display: block;margin: auto;"/>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script>

  function chekOrderStatus() {
    var loop=true;
    $.ajax({
      url: 'index.php?route=extension/payment/ott_alipay/isOrderPaid&order_id=<?php echo $order_id; ?>',
      dataType: 'json',
      success: function(json) {
        if (json['result']) {
          loop = false;
          location.href = "<?php echo $action_success; ?>";
        }
        if (loop) {
          setTimeout("chekOrderStatus()", 1000);
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }
  chekOrderStatus();
</script>
<?php echo $footer; ?> 