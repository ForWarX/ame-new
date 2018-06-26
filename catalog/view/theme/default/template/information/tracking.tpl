<?php echo $headering; ?>
<div class="container">

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
      <h3><?php echo $text_location; ?></h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <?php if ($image) { ?>
            <div class="col-sm-3"><img src="<?php echo $image; ?>" alt="<?php echo $store; ?>" title="<?php echo $store; ?>" class="img-thumbnail" /></div>
            <?php } ?>
            <div class="col-sm-3"><strong><?php echo $store; ?></strong><br />
              <address>
              <?php echo $address; ?>
              </address>
              <?php if ($geocode) { ?>
              <a href="https://maps.google.com/maps?q=<?php echo urlencode($geocode); ?>&hl=<?php echo $geocode_hl; ?>&t=m&z=15" target="_blank" class="btn btn-info"> <?php echo $button_map; ?></a>
              <?php } ?>
            </div>
            <div class="col-sm-3"><strong><?php echo $text_telephone; ?></strong><br>
              <?php echo $telephone; ?><br />
              <br />
              <?php if ($fax) { ?>
              <strong><?php echo $text_fax; ?></strong><br>
              <?php echo $fax; ?>
              <?php } ?>
            </div>
            <div class="col-sm-3">
              <?php if ($open) { ?>
              <strong><?php echo $text_open; ?></strong><br />
              <?php echo $open; ?><br />
              <br />
              <?php } ?>
              <?php if ($comment) { ?>
              <strong><?php echo $text_comment; ?></strong><br />
              <?php echo $comment; ?>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php if ($locations) { ?>
      <h3><?php echo $text_store; ?></h3>
      <div class="panel-group" id="accordion">
        <?php foreach ($locations as $location) { ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"><a href="#collapse-location<?php echo $location['location_id']; ?>" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><?php echo $location['name']; ?> <i class="fa fa-caret-down"></i></a></h4>
          </div>
          <div class="panel-collapse collapse" id="collapse-location<?php echo $location['location_id']; ?>">
            <div class="panel-body">
              <div class="row">
                <?php if ($location['image']) { ?>
                <div class="col-sm-3"><img src="<?php echo $location['image']; ?>" alt="<?php echo $location['name']; ?>" title="<?php echo $location['name']; ?>" class="img-thumbnail" /></div>
                <?php } ?>
                <div class="col-sm-3"><strong><?php echo $location['name']; ?></strong><br />
                  <address>
                  <?php echo $location['address']; ?>
                  </address>
                  <?php if ($location['geocode']) { ?>
                  <a href="https://maps.google.com/maps?q=<?php echo urlencode($location['geocode']); ?>&hl=<?php echo $geocode_hl; ?>&t=m&z=15" target="_blank" class="btn btn-info"> <?php echo $button_map; ?></a>
                  <?php } ?>
                </div>
                <div class="col-sm-3"> <strong><?php echo $text_telephone; ?></strong><br>
                  <?php echo $location['telephone']; ?><br />
                  <br />
                  <?php if ($location['fax']) { ?>
                  <strong><?php echo $text_fax; ?></strong><br>
                  <?php echo $location['fax']; ?>
                  <?php } ?>
                </div>
                <div class="col-sm-3">
                  <?php if ($location['open']) { ?>
                  <strong><?php echo $text_open; ?></strong><br />
                  <?php echo $location['open']; ?><br />
                  <br />
                  <?php } ?>
                  <?php if ($location['comment']) { ?>
                  <strong><?php echo $text_comment; ?></strong><br />
                  <?php echo $location['comment']; ?>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <?php } ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <div  class="input-group">
            <input type="text" id="ame_no" name="ame_no" value="<?php echo $ame_no; ?>" placeholder="<?php echo $text_ame_no; ?>" class="form-control input-lg" />
  <span class="input-group-btn">
    <button type="button" class="btn btn-default btn-lg" id="button-search"><?php echo $button_track; ?></button>
  </span>
          </div>
          <?php if (!empty($order)) { ?>
          <thead>
          <tr>
            <td class="text-right"><?php echo $column_order_no; ?></td>
            <td class="text-left"><?php echo $column_receiver; ?></td>
            <td class="text-right"><?php echo $column_product; ?></td>
            <td class="text-left"><?php echo $column_status; ?></td>
            <!-- <td class="text-right"><?php echo $column_total; ?></td>-->
            <td class="text-left"><?php echo $column_date_added; ?></td>
            <td class="text-left"><?php echo $column_delivery_number; ?></td>
            <td class="text-left"><?php echo $column_track; ?></td>
          </tr>
          </thead>

          <tbody>
          <tr>
            <td class="text-right"><?php echo $order['order_no']; ?></td>
            <td class="text-left"><?php echo $order['name']; ?></td>
            <td class="text-right"><?php echo $order['products']; ?></td>
            <td class="text-left"><?php echo $order['status']; ?></td>
           <!-- <td class="text-right"><?php echo $order['total']; ?></td> -->
            <td class="text-left"><?php echo $order['date_added']; ?></td>
            <td class="text-left"><?php echo $order['delivery_number']; ?></td>
            <td class="text-right">
               <a href="" data-toggle="tooltip" title="<?php echo $button_track; ?>" class="btn btn-info" id="OrderTrackBTN" data-order_no="<?php echo $order['order_no']; ?>"><i class="fa fa-plane"></i><?php echo $button_track; ?></a>
            </td>
          </tr>
          </tbody>
          <?php } else{ ?>
          <?php  echo $error_track?>
          <?php } ?>
        </table>
      </div>
      <!--
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <fieldset>
          <legend><?php echo $text_contact; ?></legend>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></label>
            <div class="col-sm-10">
              <input type="text" name="email" value="<?php echo $email; ?>" id="input-email" class="form-control" />
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-enquiry"><?php echo $entry_enquiry; ?></label>
            <div class="col-sm-10">
              <textarea name="enquiry" rows="10" id="input-enquiry" class="form-control"><?php echo $enquiry; ?></textarea>
              <?php if ($error_enquiry) { ?>
              <div class="text-danger"><?php echo $error_enquiry; ?></div>
              <?php } ?>
            </div>
          </div>
          <?php echo $captcha; ?>
        </fieldset>
        <div class="buttons">
          <div class="pull-right">
            <input class="btn btn-primary" type="submit" value="<?php echo $button_submit; ?>" />
          </div>
        </div>
      </form>
      -->
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>

<script>
$('#button-search').bind('click', function() {
url = 'index.php?route=information/tracking';

var search = $('#content input[name=\'search\']').prop('value');

if (search) {
url += '&search=' + encodeURIComponent(search);
}

var category_id = $('#content select[name=\'category_id\']').prop('value');

if (category_id > 0) {
url += '&category_id=' + encodeURIComponent(category_id);
}

var sub_category = $('#content input[name=\'sub_category\']:checked').prop('value');

if (sub_category) {
url += '&sub_category=true';
}

var filter_description = $('#content input[name=\'description\']:checked').prop('value');

if (filter_description) {
url += '&description=true';
}

  var ame_no = $('#content input[name=\'ame_no\']').prop('value');


  if (ame_no) {
    url += '&ame_no='+ame_no;
  }

location = url;
});

$('#content input[name=\'ame_no\']').bind('keydown', function(e) {
if (e.keyCode == 13) {
$('#button-search').trigger('click');
}
});
</script>
<script>
  // ¶©µ¥×·×Ù
  $("#OrderTrackBTN").click(function () {
    $.ajax({
      async: false, // ·ÀÖ¹window.open±»À¹½Ø
      url: 'index.php?route=information/tracking/get_order_track',
      type: "POST",
      data: "order_no=" + $(this).data("order_no"),
      dataType: 'json',
      success: function (json) {
        if (json.success) {
          window.open(json.result);
        } else {
          alert(json.error);
        }
      },
      error: function (xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });

    return false;
  });


</script>