<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <!-- this is the admin wechatpay header -->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-payment" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!-- below is wechatpay main content -->
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-payment" class="form-horizontal">
          <div class="tab-content">
          <!-- merchant id -->
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="entry-ott-wechatpay-merchant-id"><?php echo $entry_ott_wechatpay_merchant_id; ?></label>
              <div class="col-sm-10">
                <input type="text" name="ott_wechatpay_merchant_id" value="<?php echo $ott_wechatpay_merchant_id; ?>" placeholder="<?php echo $entry_ott_wechatpay_merchant_id; ?>" id="entry-ott-wechatpay-merchant-id" class="form-control">
                <?php if($error_ott_wechatpay_merchant_id){ ?>
                <div class="text-danger"> <?php echo $error_ott_wechatpay_merchant_id; ?></div>
                <?php } ?>
              </div>
            </div>
            <!-- sign key -->
            <div class="form-group required">
              <label class="col-sm-2 control-label" for="entry-ott-wechatpay-sign-key"><?php echo $entry_ott_wechatpay_sign_key; ?></label>
              <div class="col-sm-10">
                <input type="text" name="ott_wechatpay_sign_key" value="<?php echo $ott_wechatpay_sign_key; ?>" placeholder="<?php echo $entry_ott_wechatpay_sign_key; ?>" id="entry-ott-wechatpay-sign-key" class="form-control">
                <?php if($error_ott_wechatpay_sign_key){ ?>
                <div class="text-danger"> <?php echo $error_ott_wechatpay_sign_key; ?></div>
                <?php } ?>
              </div>
            </div>
            <!-- payment complete status -->
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-completed-status"><?php echo $entry_completed_status; ?></label>
              <div class="col-sm-10">
                <select name="ott_wechatpay_completed_status_id" id="input-completed-status" class="form-control">
                  <?php foreach ($order_statuses as $order_status) { ?>
                    <?php if ($order_status['order_status_id'] == $ott_wechatpay_completed_status_id) { ?>

                      <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                    <?php } else { ?>
                      <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
              </div>
            </div>
            <!-- status -->
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
              <div class="col-sm-10">
                <select name="ott_wechatpay_status" id="input-status" class="form-control">
                  <?php if($ott_wechatpay_status){ ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <!-- status div end -->
            <!-- sort order -->
            <div class="form-group">
              <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
              <div class="col-sm-10">
                <input type="text" name="ott_wechatpay_sort_order" value="<?php echo $ott_wechatpay_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 