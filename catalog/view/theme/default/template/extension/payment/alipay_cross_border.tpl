<form id='alipaysubmit' name='alipaysubmit' action="<?php echo $action;?>" method='get' target="_blank">
<?php while (list ($key, $val) = each ($para)) { ?>
    <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
<?php } ?>
<div class="buttons">
    <div class="pull-right">
        <a onclick="display_modal(); $('#alipaysubmit').submit();" class="btn btn-primary"><span><?php echo $button_confirm; ?></span></a>
    </div>
</div>
</form>
<div class="modal fade" tabindex="-1" role="dialog" id="dialog-confirm" style="display:none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $title_success_to_pay_or_not; ?></h4>
      </div>
      <div class="modal-body">
        <p><?php echo $alipay_confirmation_notice; ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="button_success_pay" onclick="confirmPayment();"><?php echo $button_success_pay; ?></button>
        <button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo $button_fail_pay; ?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
  function display_modal() {
    $('#dialog-confirm').modal({'show':true, 'backdrop':'static'})
  }
  function confirmPayment() {
    location.href = 'index.php?route=extension/payment/alipay_cross_border/return_url&order_id=<?php echo $order_id; ?>&sign=<?php echo $sign; ?>';
  }
</script>
