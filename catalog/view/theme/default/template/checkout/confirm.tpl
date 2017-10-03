<?php if (!isset($redirect)) { ?>
<div class="table-responsive">
  <?php if (isset($customer_group)) { ?>
    <form class="form-inline" style="margin-bottom: 10px;">
      <div class="form-group" style="margin-right: 10px;">
        <label><?php echo $text_admin_func; ?></label>
      </div>
      <div class="form-group">
        <label for="input-final-weight"><?php echo $text_final_weight; ?></label>
        <input type="text" class="form-control" id="input-final-weight" placeholder="<?php echo $text_final_weight; ?>">
      </div>
      <div class="form-group">
        <label for="input-final-price"><?php echo $text_final_price; ?></label>
        <input type="text" class="form-control" id="input-final-price" placeholder="<?php echo $text_final_price; ?>">
      </div>
      <button type="submit" class="btn btn-default" id="btn-admin-func"><i class="fa fa-thumbs-up" style="font-size: 18px;"></i></button>
    </form>
  <?php } ?>
  <table class="table table-bordered table-hover">
    <thead>
      <tr>
        <td class="text-left"><?php echo $column_name; ?></td>
        <td class="text-left"><?php echo $column_model; ?></td>
        <td class="text-right"><?php echo $column_quantity; ?></td>
        <td class="text-right"><?php echo $column_price; ?></td>
        <td class="text-right"><?php echo $column_total; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product) { ?>
      <tr>
        <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
          <?php foreach ($product['option'] as $option) { ?>
          <br />
          &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
          <?php } ?>
          <?php if($product['recurring']) { ?>
          <br />
          <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
          <?php } ?></td>
        <td class="text-left"><?php echo $product['model']; ?></td>
        <td class="text-right"><?php echo $product['quantity']; ?></td>
        <td class="text-right"><?php echo $product['price']; ?></td>
        <td class="text-right"><?php echo $product['total']; ?></td>
      </tr>
      <?php } ?>
      <?php foreach ($vouchers as $voucher) { ?>
      <tr>
        <td class="text-left"><?php echo $voucher['description']; ?></td>
        <td class="text-left"></td>
        <td class="text-right">1</td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
        <td class="text-right"><?php echo $voucher['amount']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php foreach ($totals as $total) { ?>
      <tr>
        <td colspan="4" class="text-right"><strong><?php echo $total['title']; ?>:</strong></td>
        <td class="text-right"><?php echo $total['text']; ?></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>
<?php echo $payment; ?>
<script>
    // 管理员功能
    $("#btn-admin-func").on('click', function() {
        $.ajax({
            url: '',
            success: function (res) {
                //
            }
        });

        return false;
    });
</script>
<?php } else { ?>
<script type="text/javascript"><!--
location = '<?php echo $redirect; ?>';
//--></script>
<?php } ?>
