<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-export" form="form-order" formaction="<?php echo $export; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_export; ?>" class="btn btn-info"><i class="fa fa-download"></i></button>
         <!-- <button type="submit" id="button-export2" form="form-order" formaction="<?php echo $export2; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_export2; ?>" class="btn btn-info"><i class="fa fa-download"></i></button>
          -->
          <button type="submit" id="button-import" form="form-order" formaction="<?php echo $import; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_import; ?>" class="btn btn-info"><i class="fa fa-upload"></i></button>
          <button type="submit" id="button-import2" form="form-order" formaction="<?php echo $import2; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_import2; ?>" class="btn btn-info"><i class="fa fa-upload"></i></button>
          <button type="submit" id="button-shipping" form="form-order" formaction="<?php echo $shipping; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_shipping_print; ?>" class="btn btn-info"><i class="fa fa-truck"></i></button>
        <button type="submit" id="button-invoice" form="form-order" formaction="<?php echo $invoice; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $button_invoice_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
        <a href="<?php echo $new_order; ?>" data-toggle="tooltip" title="<?php echo $button_new_order; ?>" class="btn btn-primary" target="_blank"><i class="fa fa-file"></i></a>
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" id="button-delete" form="form-order" formaction="<?php echo $delete; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
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
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
                <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control" />
              </div>
                <div class="form-group">
                    <label class="control-label" for="input-sender"><?php echo $entry_sender; ?></label>
                    <input type="text" name="filter_sender" value="<?php echo $filter_sender; ?>" placeholder="<?php echo $entry_sender; ?>" id="input-sender" class="form-control" />
                </div>

              <!-- add phone number search -->
              <div class="form-group">
                <label class="control-label" for="input-payment_phone"><?php echo $entry_payment_phone; ?></label>
                <input type="text" name="filter_payment_phone" value="<?php echo $filter_payment_phone; ?>" placeholder="<?php echo $entry_payment_phone; ?>" id="input-payment_phone" class="form-control" />
              </div>

                <div class="form-group">
                    <label class="control-label" for="input-anything"><?php echo $entry_anything; ?></label>
                    <input type="text" name="filter_anything" value="<?php echo $filter_anything; ?>" placeholder="<?php echo $entry_anything; ?>" id="input-anything" class="form-control" />
                </div>

            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                <select name="filter_order_status" id="input-order-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_order_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_missing; ?></option>
                  <?php } ?>
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <!-- Change search total to search recipient -->
              <div class="form-group">
                <label class="control-label" for="input-recipient"><?php echo $entry_recipient; ?></label>
                <input type="text" name="filter_recipient" value="<?php echo $filter_recipient; ?>" placeholder="<?php echo $entry_recipient; ?>" id="input-total" class="form-control" />
              </div>
              <!-- add search search shipping phone number -->
              <div class="form-group">
                <label class="control-label" for="input-shipping-phone"><?php echo $entry_shipping_phone; ?></label>
                <input type="text" name="filter_shipping_phone" value="<?php echo $filter_shipping_phone; ?>" placeholder="<?php echo $entry_shipping_phone; ?>" id="input-shipping-phone" class="form-control" />
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                <div class="input-group date">
                      <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <!-- Change search date modified to shipping number-->
                <div class="form-group">
                    <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                    <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
                </div>
              <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            <!--  <button type="button" id="button-change-customer" class="btn btn-primary"><i class="fa fa-user"></i> <?php echo $button_change_customer; ?></button>
           -->
            </div>
          </div>
        </div>
        <form method="post" action="" enctype="multipart/form-data" id="form-order">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'ame') { ?>
                    <a href="<?php echo $sort_ame; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ame; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ame; ?>"><?php echo $column_ame; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php echo $column_sender; ?></td>
                  <td class="text-left"><?php if ($sort == 'shipping_name') { ?>
                    <a href="<?php echo $sort_shipper; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_shipper; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_shipper; ?>"><?php echo $column_shipper; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_weight; ?></td>
                    <td class="text-left"><?php echo $column_shipperphone; ?></td>
                    <td class="text-left"><?php echo $column_shipperid; ?></td>
                    <!--
                  <td class="text-left"><?php if ($sort == 'o.delivery_company') { ?>
                    <a href="<?php echo $sort_delivery_company; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_delivery_company; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_delivery_company; ?>"><?php echo $column_delivery_company; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'o.order_delivery_number') { ?>
                    <a href="<?php echo $sort_delivery_number; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_delivery_number; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_delivery_number; ?>"><?php echo $column_delivery_number; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php echo $column_custom_pass; ?></td>

                  -->
                  <td class="text-left"><?php echo $column_paid; ?></td>
                    <td class="text-left"><?php if ($sort == 'o.order_delivery_number') { ?>
                        <a href="<?php echo $sort_delivery_number; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_delivery_number; ?></a>
                        <?php } else { ?>
                        <a href="<?php echo $sort_delivery_number; ?>"><?php echo $column_delivery_number; ?></a>
                        <?php } ?></td>
                    <!--
               <td class="text-left"><?php if ($sort == 'storage_name') { ?>
                 <a href="<?php echo $sort_storage; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_storage; ?></a>
                 <?php } else { ?>
                 <a href="<?php echo $sort_storage; ?>"><?php echo $column_storage; ?></a>
                 <?php } ?></td>
                  -->

               <td class="text-left"><?php if ($sort == 'order_status') { ?>
                 <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                 <?php } else { ?>
                 <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                 <?php } ?></td>
               <td class="text-right"><?php echo $column_action; ?></td>
             </tr>
           </thead>
           <tbody>
             <?php if ($orders) { ?>
             <?php foreach ($orders as $order) { ?>
             <tr>
               <td class="text-center"><?php if (in_array($order['order_id'], $selected)) { ?>
                 <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" checked="checked" />
                 <?php } else { ?>
                 <input type="checkbox" name="selected[]" value="<?php echo $order['order_id']; ?>" />
                 <?php } ?>
                 <input type="hidden" name="shipping_code[]" value="<?php echo $order['shipping_code']; ?>" /></td>
               <td class="text-right"><?php echo $order['invoice_prefix']; ?></td>
               <td class="text-left"><?php echo $order['date_added']; ?></td>
               <td class="text-left"><?php echo $order['customer']; ?></td>
                 <td class="text-left"><?php echo $order['payment_firstname']; ?></td>
               <td class="text-left"><?php echo $order['shipping_name']; ?></td>
               <td class="text-left"><?php echo $order['weight']; ?></td>
                 <td class="text-left"><?php echo $order['shipping_phone']; ?></td>
                 <td class="text-left"><?php echo $order['shipping_chinaid']; ?></td>
                 <!--
               <td class="text-left"><?php echo $order['delivery_company']; ?></td>

               <td class="text-left"><?php echo $order['delivery_number']; ?></td>

               <td class="text-left"><?php echo $order['custome_pass']; ?></td>
               -->

                  <td class="text-left"><?php echo ( ($order['order_status_id'] == 2)||($order['order_status_id'] == 3)||($order['order_status_id'] == 5)||($order['order_status_id'] == 15)) ? 'Paid' : 'No'; ?></td>
                    <!--<td class="text-left"><?php echo $order['storage_name']; ?></td> -->
                    <td class="text-left"><?php echo $order['delivery_number']; ?></td>
                 <td class="text-left"><?php echo $order['order_status']; ?></td>
                 <td class="text-right">
                   <a href="<?php echo $order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-info"><i class="fa fa-eye"></i></a>
                   <a href="<?php echo $order['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                   <a href="<?php echo $order['order_copy']; ?>" data-toggle="tooltip" title="<?php echo $button_order_copy; ?>" class="btn btn-primary" target="_blank"><i class="fa fa-copy"></i></a>
                     <a href="<?php echo $order['label'];; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $button_labels_print; ?>" class="btn btn-info"><i class="fa fa-barcode"></i></a>

                 </td>
               </tr>
               <?php } ?>
               <?php } else { ?>
               <tr>
                 <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
               </tr>
               <?php } ?>
             </tbody>
           </table>
         </div>
       </form>
       <div class="row">
         <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
         <div class="col-sm-6 text-right"><?php echo $results; ?></div>
       </div>
     </div>
   </div>
 </div>
 <script type="text/javascript">
     $('#button-filter').on('click', function() {
         url = 'index.php?route=sale/order&token=<?php echo $token; ?>';

         var filter_order_id = $('input[name=\'filter_order_id\']').val();
         if (filter_order_id) {
             url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
         }

         var filter_customer = $('input[name=\'filter_customer\']').val();
         if (filter_customer) {
             url += '&filter_customer=' + encodeURIComponent(filter_customer);
         }

         var filter_order_status = $('select[name=\'filter_order_status\']').val();
         if (filter_order_status != '*') {
             url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
         }

         var filter_total = $('input[name=\'filter_total\']').val();
         if (filter_total) {
             url += '&filter_total=' + encodeURIComponent(filter_total);
         }
         //add filter recipient
         var filter_recipient = $('input[name=\'filter_recipient\']').val();

         if (filter_recipient) {
             url += '&filter_recipient=' + encodeURIComponent(filter_recipient);
         }

         //add filter telephone
         var filter_telephone = $('input[name=\'filter_telephone\']').val();

         if (filter_telephone) {
             url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
         }
         //add filter payment phone
         var filter_payment_phone = $('input[name=\'filter_payment_phone\']').val();

         if (filter_payment_phone) {
             url += '&filter_payment_phone=' + encodeURIComponent(filter_payment_phone);
         }

         //add filter shipping phone
         var filter_shipping_phone = $('input[name=\'filter_shipping_phone\']').val();

         if (filter_shipping_phone) {
             url += '&filter_shipping_phone=' + encodeURIComponent(filter_shipping_phone);
         }

         var filter_date_added = $('input[name=\'filter_date_added\']').val();

         if (filter_date_added) {
             url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
         }

         var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

         if (filter_date_modified) {
             url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
         }
         //add filter shipping number
         var filter_shipping_number = $('input[name=\'filter_shipping_number\']').val();

         if (filter_shipping_number) {
             url += '&filter_shipping_number=' + encodeURIComponent(filter_shipping_number);
         }
         //add filter Sender
         var filter_sender = $('input[name=\'filter_sender\']').val();

         if (filter_sender) {
             url += '&filter_sender=' + encodeURIComponent(filter_sender);
         }
         //add filter anything
         var filter_anything = $('input[name=\'filter_anything\']').val();

         if (filter_anything) {
             url += '&filter_anything=' + encodeURIComponent(filter_anything);
         }

         location = url;
     });
     $('#content input[name=\'filter_order_id\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_customer\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_recipient\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_payment_phone\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_shipping_phone\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_sender\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
     $('#content input[name=\'filter_anything\']').bind('keydown', function(e) {
         if (e.keyCode == 13) {
             $('#button-filter').trigger('click');
         }
     });
 </script>


 <script type="text/javascript">
     // 更换订单用户
     $('#button-change-customer').on('click', function() {
         if (!confirm("Confirm?")) return;

         url = 'index.php?route=sale/order&token=<?php echo $token; ?>';

         var filter_order_id = $('input[name=\'filter_order_id\']').val();
         if (filter_order_id) {
             url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
         }

         var filter_customer = '<?php echo $filter_customer; ?>';
         if (filter_customer) {
             url += '&filter_customer=' + encodeURIComponent(filter_customer);
         }

         var filter_order_status = $('select[name=\'filter_order_status\']').val();
         if (filter_order_status != '*') {
             url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
         }

         var filter_total = $('input[name=\'filter_total\']').val();
         if (filter_total) {
             url += '&filter_total=' + encodeURIComponent(filter_total);
         }
         //add filter recipient
         var filter_recipient = $('input[name=\'filter_recipient\']').val();

         if (filter_recipient) {
             url += '&filter_recipient=' + encodeURIComponent(filter_recipient);
         }

         //add filter telephone
         var filter_telephone = $('input[name=\'filter_telephone\']').val();

         if (filter_telephone) {
             url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
         }

         //add filter shipping phone
         var filter_shipping_phone = $('input[name=\'filter_shipping_phone\']').val();

         if (filter_shipping_phone) {
             url += '&filter_shipping_phone=' + encodeURIComponent(filter_shipping_phone);
         }

         var filter_date_added = $('input[name=\'filter_date_added\']').val();

         if (filter_date_added) {
             url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
         }

         var filter_date_modified = $('input[name=\'filter_date_modified\']').val();

         if (filter_date_modified) {
             url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
         }
         //add filter shipping number
         var filter_shipping_number = $('input[name=\'filter_shipping_number\']').val();

         if (filter_shipping_number) {
             url += '&filter_shipping_number=' + encodeURIComponent(filter_shipping_number);
         }

         var change_customer = $('input[name=\'filter_customer\']').data('id');
         if (change_customer) {
             url += '&change_customer=' + change_customer;
         }

         $('#form-order').attr('action', url).submit();
     });
 </script>
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
       $('input[name=\'filter_customer\']').val(item['label']).data("id", item['value']);
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
      $('#button-export').on('click', function(e) {
          $('#form-order').attr('action', this.getAttribute('formAction'));

      });
      $('#button-export2').on('click', function(e) {
          $('#form-order').attr('action', this.getAttribute('formAction'));

      });
//--></script> 
  <script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
  <link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
  <script  type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?> 