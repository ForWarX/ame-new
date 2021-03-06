<?php if ($error_warning) { ?>
<div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
<?php } ?>

<?php if (isset($order)) { ?>
<?php $box_num = 1; ?>
<table class="table table-bordered table-hover">
  <tbody>
  <?php foreach ($order as $order_per_box) { ?>
    <?php unset($order_per_box[0]); unset($order_per_box[1]); // 去除路线和箱子名 ?>
    <tr>
      <td style="vertical-align: middle;"><?php echo $text_box . ' ' . $box_num; ?></td>
      <td class="text-left">
        <?php $isFirst = true; ?>
        <?php foreach ($order_per_box as $product) { ?>
          <?php if (!$isFirst) { ?>
            <?php echo "<br>"; ?>
          <?php } else { ?>
            <?php $isFirst = false; ?>
          <?php } ?>
          <?php echo $product['quantity'] . ' X ' . $product['name']; ?>
        <?php } ?>
      </td>
    </tr>
    <?php $box_num++; ?>
  <?php } ?>
  </tbody>
</table>
<?php } ?>

<?php if ($shipping_methods) { ?>
<p><?php echo $text_shipping_method; ?></p>
<?php foreach ($shipping_methods as $shipping_method) { ?>
<p><strong><?php echo $shipping_method['title']; ?></strong></p>
<?php if (!$shipping_method['error']) { ?>
<?php foreach ($shipping_method['quote'] as $quote) { ?>
<div class="radio">
  <label>
    <?php if ($quote['code'] == $code || !$code) { ?>
    <?php $code = $quote['code']; ?>
    <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" checked="checked" />
    <?php } else { ?>
    <input type="radio" name="shipping_method" value="<?php echo $quote['code']; ?>" />
    <?php } ?>
    
		       <?php if(isset($quote['logo']) && $quote['logo']) echo '<img src="'.$quote['logo'].'"/>'; ?> <?php echo $quote['title']; ?> - <?php echo $quote['text']; ?>
			

		       <?php
			     if(isset($quote['desc']) && $quote['desc']) echo html_entity_decode($quote['desc']);
			   ?> 
			
  </label>
</div>
<?php } ?>
<?php } else { ?>
<div class="alert alert-danger"><?php echo $shipping_method['error']; ?></div>
<?php } ?>
<?php } ?>
<?php } ?>
<p><strong><?php echo $text_comments; ?></strong></p>
<p>
  <textarea name="comment" rows="8" class="form-control"><?php echo $comment; ?></textarea>
</p>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_continue; ?>" id="button-shipping-method" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
  </div>
</div>
