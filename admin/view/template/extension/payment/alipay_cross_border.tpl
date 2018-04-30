<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
	<div class="container-fluid">
	  <div class="pull-right">
		<button type="submit" form="form-alipay-cross-border" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary" onclick="$('#form').submit();"><i class="fa fa-save"></i></button>
		<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a> 
          </div>
	  <h1><?php echo $heading_title; ?></h1>
	  <ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	  </ul>
	</div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php } ?>
        <div class="panel panel-default">
	    <div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
	    </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
                  <div class="form-group required">
                        <label class="col-sm-2 control-label" for="entry-seller_email"><?php echo $entry_seller_email; ?></label>
                        <div class="col-sm-10">
                          <input type="text" name="alipay_cross_border_seller_email" value="<?php echo $alipay_cross_border_seller_email; ?>" class="form-control" />
                            <?php if ($error_email) { ?>
                            <div class="text-danger"><?php echo $error_email; ?></div>
                            <?php } ?>
                        </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_security_code"><?php echo $entry_security_code; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="alipay_cross_border_security_code" value="<?php echo $alipay_cross_border_security_code; ?>" class="form-control" />
                        <?php if ($error_secrity_code) { ?>
                          <div class="text-danger"><?php echo $error_secrity_code; ?></div>
                        <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_partner"><?php echo $entry_partner; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="alipay_cross_border_partner" value="<?php echo $alipay_cross_border_partner; ?>" class="form-control" />
                        <?php if ($error_partner) { ?>
                          <div class="text-danger"><?php echo $error_partner; ?></div>
                        <?php } ?>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_currency_code"><?php echo $entry_currency_code; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_currency_code" class="form-control">
                        <?php foreach ($currencies as $currency) { ?>
                        <?php if ($currency['code'] == $alipay_cross_border_currency_code) { ?>
                        <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_order_status"><?php echo $entry_order_status; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_order_status_id" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if ($order_status['order_status_id'] == $alipay_cross_border_order_status_id) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_trade_finished"><?php echo $entry_trade_finished; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_trade_finished" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if ($order_status['order_status_id'] == $alipay_cross_border_trade_finished) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group required">
                    <label class="col-sm-2 control-label" for="entry_trade_closed"><?php echo $entry_trade_closed; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_trade_closed" class="form-control">
                        <?php foreach ($order_statuses as $order_status) { ?>
                        <?php if ($order_status['order_status_id'] == $alipay_cross_border_trade_closed) { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_geo_zone_id" id="input-geo-zone" class="form-control">
                        <option value="0"><?php echo $text_all_zones; ?></option>
                        <?php foreach ($geo_zones as $geo_zone) { ?>
                        <?php if ($geo_zone['geo_zone_id'] == $alipay_cross_border_geo_zone_id) { ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="entry_sort_order"><?php echo $entry_sort_order; ?></label>
                    <div class="col-sm-10">
                      <input type="text" name="alipay_cross_border_sort_order" value="<?php echo $alipay_cross_border_sort_order; ?>" size="1" class="form-control" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-2 control-label" for="entry_status"><?php echo $entry_status; ?></label>
                    <div class="col-sm-10">
                      <select name="alipay_cross_border_status" class="form-control">
                        <?php if ($alipay_cross_border_status) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <tr>
                    <td></td>
                    <td></td>
                  </tr>
              </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?> 