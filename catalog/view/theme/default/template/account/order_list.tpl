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
      <h1><?php echo $heading_title; ?></h1>
      <?php if ($orders) { ?>
        <!--add a filter-->
        <div class="well">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="input-order-id"><?php echo $column_order_no; ?></label>
                        <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" for="input-receiver"><?php echo $column_receiver ?></label>
                        <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_receiver; ?>" id="input-receiver" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-3">

                    <div class="form-group">
                        <label class="control-label" for="input-status"><?php echo $column_status; ?></label>
                        <select id="input-status" class="form-control" name="filter_status">
                            <option></option>
                            <?php foreach($order_status as $status) { ?>
                            <option value="<?php echo $status['order_status_id']; ?>" <?php if (!empty($filter_status) && $filter_status == $status['order_status_id']) { ?>selected<?php } ?>><?php echo $status['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">

                    <div class="form-group">
                        <label class="control-label" for="input-date-added"><?php echo $column_date_added; ?></label>
                        <div class="input-group date">
                            <input type="date" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                          </div>
                    </div>
                    <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
                </div>
            </div>
        </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <td class="text-right"><?php echo $column_order_no; ?></td>
              <td class="text-left"><?php echo $column_receiver; ?></td>
              <td class="text-right"><?php echo $column_product; ?></td>
              <td class="text-left"><?php echo $column_status; ?></td>
              <td class="text-right"><?php echo $column_total; ?></td>
              <td class="text-left"><?php echo $column_date_added; ?></td>
              <td></td>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order) { ?>
            <tr>
              <td class="text-right"><?php echo $order['order_no']; ?></td>
              <td class="text-left"><?php echo $order['name']; ?></td>
              <td class="text-right"><?php echo $order['products']; ?></td>
              <td class="text-left"><?php echo $order['status']; ?></td>
              <td class="text-right"><?php echo $order['total']; ?></td>
              <td class="text-left"><?php echo $order['date_added']; ?></td>
              <td class="text-right">
                <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                <a href="" data-toggle="tooltip" title="<?php echo $button_track; ?>" class="btn btn-info" id="OrderTrackBTN" data-order_no="<?php echo $order['order_no']; ?>"><i class="fa fa-plane"></i></a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <div class="row">
        <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
        <div class="col-sm-6 text-right"><?php echo $results; ?></div>
      </div>
      <?php } else { ?>
      <p><?php echo $text_empty; ?></p>
      <?php } ?>
      <div class="buttons clearfix">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<script>
    // 订单追踪
    $("#OrderTrackBTN").click(function () {
        $.ajax({
            async: false, // 防止window.open被拦截
            url: 'index.php?route=account/order/get_order_track',
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

<!--add the javascript for filter-->
<script type="text/javascript"><!--
    $('#button-filter').on('click', function() {
        url = 'index.php?route=account/order';

        var filter_order_id = $('input[name=\'filter_order_id\']').val();

        if (filter_order_id) {
            url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
        }

        var filter_customer = $('input[name=\'filter_customer\']').val();

        if (filter_customer) {
            url += '&filter_customer=' + encodeURIComponent(filter_customer);
        }

        var filter_status = $('select[name=\'filter_status\']').val();

        if (filter_status != '*') {
            url += '&filter_status=' + encodeURIComponent(filter_status);
        }

        var filter_date_added = $('input[name=\'filter_date_added\']').val();

        if (filter_date_added) {
            url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
        }

        var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

        if (filter_date_modified) {
            url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
        }

        location = url;
    });
    //--></script>
<script type="text/javascript"><!--
    $('input[name=\'filter_customer\']').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['customer_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name=\'filter_customer\']').val(item['label']);
        }
    });
    //--></script>
<script type="text/javascript"><!--
    $('input[name^=\'selected\']').on('change', function() {
        $('#button-shipping, #button-invoice').prop('disabled', true);

        var selected = $('input[name^=\'selected\']:checked');

        if (selected.length) {
            $('#button-invoice').prop('disabled', false);
        }

        for (i = 0; i < selected.length; i++) {
            if ($(selected[i]).parent().find('input[name^=\'shipping_code\']').val()) {
                $('#button-shipping').prop('disabled', false);

                break;
            }
        }
    });

    $('#button-shipping, #button-invoice').prop('disabled', true);

    $('input[name^=\'selected\']:first').trigger('change');

    // IE and Edge fix!
    $('#button-shipping, #button-invoice').on('click', function(e) {
        $('#form-order').attr('action', this.getAttribute('formAction'));
    });

    $('#button-delete').on('click', function(e) {
        $('#form-order').attr('action', this.getAttribute('formAction'));

        if (confirm('<?php echo $text_confirm; ?>')) {
            $('#form-order').submit();
        } else {
            return false;
        }
    });
    //--></script>


<?php echo $footer; ?>
