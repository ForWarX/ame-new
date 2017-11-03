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
		<div id="content" class="<?php echo $class; ?>">
			<?php echo $content_top; ?>
			<div id="warning_box" class="alert alert-warning warningtxt" style="display:none;">
				<?php if (!empty($err_message)) { ?>
				<strong>Warning!</strong> <?php echo $err_message; ?>
				<?php } ?>
			</div>
			<form action='<?php echo $action_url; ?>' method='POST'  enctype="multipart/form-data" class='form-group' id='formproduct'>
				<div class="container" style="background: #f8f8f8; margin-bottom: 10px; padding: 15px; width: 100%;">
					<input type='hidden' name="address_id" value='<?php echo empty($user_address) ? '' : $user_address['address_id']; ?>'>
					<div class="row">
						<div class='col-sm-12' style="font-size: larger; color: black;">Sender:</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Name:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="firstname" value='<?php echo empty($user_address) ? 'AME' : $user_address['firstname']; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Company:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="company" value='<?php echo empty($user_address) ? 'AmericoMall' : $user_address['company']; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>City:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="city" value='<?php echo empty($user_address) ? 'Toronto' : $user_address['city']; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label' for='zone-id'>Province:<span style="color:red">*</span></label>
								<div class='col-sm-4'>
									<select name="zone_id" id="zone_id" class="form-control input-sm">
										<?php $zone_id = empty($user_address) ? 610 : $user_address["zone_id"]; ?>
										<?php foreach ($zones as $zone) { ?>
											<option value="<?php echo $zone['zone_id']; ?>" <?php echo ($zone['zone_id'] == $zone_id) ? 'selected' : ''; ?>><?php echo $zone['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-12'>
							<div class="row form-group">
								<label class='col-sm-1 control-label'>Address:<span style="color:red">*</span></label>
								<div class='col-sm-8'><input class="form-control input-sm" type='text' name="address_1" value='<?php echo empty($user_address) ? '3445 Sheppard Ave East' : $user_address['address_1']; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Postcode:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="postcode" value='<?php echo empty($user_address) ? 'M1T 3K5' : $user_address['postcode']; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label' for='country-id'>Country:<span style="color:red">*</span></label>
								<div class='col-sm-4'>
									<select name="country_id" id="country_id" class="form-control input-sm">
										<?php $country_id = empty($user_address) ? 38 : $user_address["country_id"]; ?>
										<?php foreach ($countries as $country) { ?>
											<option value="<?php echo $country['country_id']; ?>" <?php echo ($country['country_id'] == $country_id) ? 'selected' : ''; ?>><?php echo $country['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Email:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="email" value='<?php echo empty($user_address) ? '' : $user_address['email']; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Phone:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="phone" value='<?php echo empty($user_address) ? '647-498-8891' : $user_address['phone']; ?>'></div>
							</div>
						</div>
					</div>
				</div>
				<?php if (!empty($shipping_address_list)) { ?>
				<div class="form-group">
					<select name='shipping_select' id='select_shipping_address' class="form-control input-sm">
						<option value=0>--Select Reciptient--</option>
						<?php foreach ($shipping_address_list as $key => $shipping_address) { ?>
							<option value=<?php echo $key; ?>><?php echo $shipping_address['firstname'] . " - " . $shipping_address['address_1']; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php } ?>
				<div class="container" style="background: #f8f8f8; margin-bottom: 10px; padding: 15px; width: 100%;">
					<input type='hidden' name="shipping_address_id" value=''>
					<div class="row">
						<div class='col-sm-12' style="font-size: larger; color: black;">Recipient:</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Name:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_firstname" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["name"]; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Company:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_company" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["company"]; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">

						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label' for='shipping-country-id'>Country:<span style="color:red">*</span></label>
								<div class='col-sm-4'>
									<select name="shipping_country_id" id='shipping_country_id' class="form-control input-sm">
										<?php $country_id = empty($shipping_copy) ? 38 : $shipping_copy["country_id"]; ?>
										<option value="0"> -- Select -- </option>
										<?php foreach ($countries as $country) { ?>
										<option value="<?php echo $country['country_id']; ?>" <?php echo ($country['country_id'] == $country_id) ? 'selected' : ''; ?>><?php echo $country['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label' for='shipping-zone-id'>Province:<span style="color:red">*</span></label>
								<div class='col-sm-4'>
									<select name="shipping_zone_id" id="shipping_zone_id" class="form-control input-sm">
										<?php
											if (!empty($shipping_copy)) $zone_id = $shipping_copy["zone_id"];
											elseif (!empty($user_address)) $zone_id = $user_address["zone_id"];
											else $zone_id = 0;
											if (empty($shipping_zones)) $shipping_zones = $zones;
										?>
										<option value="0"> -- Select -- </option>
										<?php foreach ($shipping_zones as $zone) { ?>
											<option value="<?php echo $zone['zone_id']; ?>" <?php echo ($zone['zone_id'] == $zone_id) ? 'selected' : ''; ?>><?php echo $zone['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-12'>
							<div class="row form-group">
								<label class='col-sm-1 control-label'>Address:<span style="color:red">*</span></label>
								<div class='col-sm-8'><input class="form-control input-sm" type='text' name="shipping_address_1" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["address_1"]; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Postcode:</label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_postcode" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["postcode"]; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>City:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_city" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["city"]; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>District:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_city" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["city"]; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Email:<span style="color:red"></span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_email" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["email"]; ?>'></div>
							</div>
						</div>
						<div class='col-sm-6'>
							<div class="row form-group">
								<label class='col-sm-2 control-label'>Phone:<span style="color:red">*</span></label>
								<div class='col-sm-4'><input class="form-control input-sm" type='text' name="shipping_phone" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["phone"]; ?>'></div>
							</div>
						</div>
					</div>
					<div class="row" id='china_div' style="display:none;">
						<div class='col-sm-4'>
							<div class="row form-group">
								<label class='col-sm-3 control-label'>China ID #:<span style="color:red">*</span></label>
								<div class='col-sm-6'><input class="form-control input-sm" type='text' name="chinaid" value='<?php if (!empty($shipping_copy)) echo $shipping_copy["chinaid"]; ?>'></div>
							</div>
						</div>
						<div class='col-sm-4'>
							<div class="row form-group">
								<label class='col-sm-3 control-label'>China ID Front:</label>
								<div class='col-sm-9'>
									<input type='file' name="chinaid_front" value=''>
									<p class="help-block" id="chinaid_front_helper" style="display: none;"><?php echo $text_chinaid_front_helper; ?></p>
								</div>
							</div>
						</div>
						<div class='col-sm-4'>
							<div class="row form-group">
								<label class='col-sm-3 control-label'>China ID Back:</label>
								<div class='col-sm-9'>
									<input type='file' name="chinaid_back" value=''>
									<p class="help-block" id="chinaid_back_helper" style="display: none;"><?php echo $text_chinaid_back_helper; ?></p>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="container product_item" style="background: #f8f8f8; padding: 15px; width: 100%; border-bottom: 1px solid #a2a8b1;">
					<div class="row product">
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class='col-sm-12 control-label'>UPC<span style="color:red">*</span></label>
								<div class='col-sm-12'><input class="form-control input-sm upc" type='text' name="upc[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class='col-sm-12 control-label'>Brand</label>
								<div class='col-sm-12'><input class="form-control input-sm mpn" type='text' name="mpn[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class='col-sm-12 control-label'>English Name</label>
								<div class='col-sm-12'><input class="form-control input-sm meta_title" type='text' name="meta_title[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row form-group">
								<label  class='col-sm-12 control-label'>Chinese Name<span style="color:red">*</span></label>
								<div class='col-sm-12' ><input class="form-control input-sm chn" type='text' name="name[]" value=''></div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class='col-sm-12 control-label'>Spec</label>
								<div class='col-sm-12'><input class="form-control input-sm spec" type='text' name="tag[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class='col-sm-12 control-label'>Quantity<span style="color:red">*</span></label>
								<div class='col-sm-12'><input class="form-control input-sm quantity" type='text' name="quantity[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row form-group">
								<label class="col-sm-12 control-label">Unit Value<span style="color:red">*</span></label>
								<div class='col-sm-12'><input class="form-control input-sm price" type='text' name="price[]" value=''></div>
							</div>
						</div>
						<div class='col-sm-3'>
							<div class="row">
								<label class='col-sm-12'>&nbsp;</label>
								<a href="#" class="btn btn-default remove" role="button" style="margin-left: 15px;">Remove</a>
								<input type='hidden' name="prod_id[]" value='' class='prod_id'>
								<input type='hidden' name="category_id[]" value='' class='category_id'>
								<input type="hidden" name="canMix[]" value="" class="canMix">
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<?php if (isset($admin_customer)) { ?>
					<div class='col-sm-3'>
						<div class="row form-group">
							<label class="col-sm-12 control-label"><b>Category(Admin)</b></label>
							<div class='col-sm-12'><input class="form-control input-sm" id="input-admin-category" type='text' name="admin_category" value='' readonly></div>
						</div>
					</div>
					<?php } ?>
					<div class='col-sm-3'>
						<div class="row form-group">
							<label class="col-sm-12 control-label"><b>Weight</b></label>
							<div class='col-sm-12'><input class="form-control input-sm" id="input-admin-weight" type='text' name="admin_weight" value='' placeholder="LB"></div>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class="row form-group">
							<label class="col-sm-12 control-label"><b>Total Price</b></label>
							<div class='col-sm-12'><input class="form-control input-sm" id="input-admin-total" type='text' name="admin_total" value='' placeholder="C$"></div>
						</div>
					</div>
					<div class='col-sm-3'>
						<div class="row form-group">
							<label class="col-sm-12 control-label"><b>Comment</b></label>
							<div class='col-sm-12'><input class="form-control input-sm" id="input-admin-comment" type='text' name="admin_comment" value=''></div>
						</div>
					</div>
				</div>
				<div class="row" style="margin-top: 15px;">
					<div class='col-sm-6 col-xs-6'>
						<a href="#" class="btn btn-primary addmore" role="button">Add Item</a>
					</div>
					<div class='col-sm-6 col-xs-6'>
						<input type='submit' class='btn btn-primary pull-right' value='Submit'>
					</div>
				</div>
			</form>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>
<script type="text/javascript">
var donealert = 0;
var availableTags = [<?php foreach ($upcs as $upc) { echo '"'.$upc.'",'; } ?>];

$('.addmore').click(function(e){
	e.preventDefault();

	donealert = 0;
	$('.product_item:last').clone().insertAfter('.product_item:last');
	$('.product_item:last').find("input[type=text], input[type=hidden], select").val('');
	$('.product_item:last').find("input[type=text], select").prop('readonly', false);
	$(".product_item:last .upc").autocomplete({ source: availableTags, select: get_product, change: get_product});

	$('.product_item:last .remove').click(removeme);
});

function check_catalog(){
	var cid = -1;
	var upc = "";
	var canMix = -1;
	var doAlert = false;
	$('.category_id').each(function(index) {
		if (donealert) return;

		var mix = $(this).parent().find(".canMix").val(); // 当前品类是否可以混合下单
        if (mix == 0 && cid == -1) {
            cid = $(this).val();
            upc = $(this).closest('.product_item').find('.upc').val();
        }
		if (canMix == -1) {
			camMix = mix;
			return;
		}
		else if (mix != canMix) doAlert = true;
		else if ((mix != 1)) { // 首个品类不能混单的情况下，之后的品类出现与首个品类不相同的情况
			doAlert = $(this).val() != cid;
		}

		if (doAlert) {
			donealert = 1;
			var upc1 = $(this).closest('.product_item').find('.upc').val();
			var txt = upc == "" ? upc : upc + ", ";
			txt += upc1 + " must shipping in different package, please remove one and place it in different order";
			alert(txt);
		}
	});
}

function get_product(e, u){
	e.preventDefault();
	if (u.item || $(this).val().length) {
		var inputval = u.item ? u.item.value : $(this).val();
		var fath = $(this).closest('.product_item');
		$(this).val(inputval);
	
		$.ajax({
			url: 'index.php?route=product/apply/get&inputupc=' +  encodeURIComponent(inputval),
			dataType: 'json',
			success: function(json) {
				if (json.status == 'OK') {
					fath.find('.mpn').val(json.product.mpn);		// Brand
					fath.find('.mpn').prop('readonly', true);
	
					fath.find('.meta_title').val(json.product.meta_title);	// English name
					fath.find('.meta_title').prop('readonly', true);
	
					fath.find('.chn').val(json.product.name);		// Chinese name
					fath.find('.chn').prop('readonly', true);
	
					fath.find('.spec').val(json.product.tag);		// spec
					fath.find('.spec').prop('readonly', true);
	
					fath.find('.price').val(json.product.price);
					fath.find('.price').prop('readonly', true);
	
					fath.find('.prod_id').val(json.product.product_id);
					fath.find('.category_id').val(json.product.category_id);
					fath.find('.canMix').val(json.product.canMix);
					check_catalog();
				}
			}
		});
	} else {
		check_catalog();
	}
}


var addresslist = [];
<?php
foreach ($shipping_address_list as $key => $shipping_address) {
	echo "addresslist[".$key."] = [];\n";
	foreach ($shipping_address as $idx => $val) {
		if ($idx != 'custom_field') {
			echo "addresslist[".$key."]['" . $idx . "'] = '" . $val . "';\n";
		}
	}
}
?>
$('#select_shipping_address').change(function(e) {
	var idx = $('#select_shipping_address').val();
	if (idx > 0) {
		$('select[name=shipping_zone_id]').val(0).data("zone_id", addresslist[idx]['zone_id']);
		$('select[name=shipping_country_id]').val(addresslist[idx]['country_id']).trigger("change"); // 触发改变后会运行别的函数清空部分数据，因此放在前面运行
		$('input[name=shipping_address_id]').val(addresslist[idx]['address_id']);
		$('input[name=shipping_firstname]').val(addresslist[idx]['firstname']);
		$('input[name=shipping_company]').val(addresslist[idx]['company']);
		$('input[name=shipping_city]').val(addresslist[idx]['city']);
		$('input[name=shipping_address_1]').val(addresslist[idx]['address_1']);
		$('input[name=shipping_postcode]').val(addresslist[idx]['postcode']);
		$('input[name=shipping_email]').val(addresslist[idx]['email']);
		$('input[name=shipping_phone]').val(addresslist[idx]['phone']);
		$('input[name=chinaid]').val(addresslist[idx]['chinaid']);
		$('input[name=chinaid_front]').val('');
		$('input[name=chinaid_back]').val('');
		if (addresslist[idx]['chinaid_front']) $("#chinaid_front_helper").show();
		else $("#chinaid_front_helper").hide();
		if (addresslist[idx]['chinaid_back']) $("#chinaid_back_helper").show();
		else $("#chinaid_back_helper").hide();
	} else {
		$('select[name=shipping_zone_id]').val(0).data("zone_id", null);
		$('select[name=shipping_country_id]').val(0).trigger("change"); // 触发改变后会运行别的函数清空部分数据，因此放在前面运行
		$('input[name=shipping_address_id]').val(0);
		$('input[name=shipping_firstname]').val('');
		$('input[name=shipping_company]').val('');
		$('input[name=shipping_city]').val('');
		$('input[name=shipping_address_1]').val('');
		$('input[name=shipping_postcode]').val('');
		$('input[name=shipping_email]').val('');
		$('input[name=shipping_phone]').val('');
		$('input[name=chinaid]').val('');
		$('input[name=chinaid_front]').val('');
		$('input[name=chinaid_back]').val('');
	}
});

function removeme(e){
	e.preventDefault();
	donealert = 0;
	
	var cnt = $('.product_item').length;
	if (cnt <= 1) {
		alert("Can't remove last one");
		return false;
	}
	$(this).closest('.product_item').remove();
}


$('.remove').click(removeme);
$(".upc").autocomplete({ source: availableTags, select: get_product, change: get_product });

</script>
<script type="text/javascript"><!--
function change_country(province_id, country_id, cur_province_id) {
	$.ajax({
		url: 'index.php?route=checkout/checkout/country&country_id=' + country_id,
		dataType: 'json',
		success: function(json) {
			html = '<option value=""> -- Select -- </option>';
			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == cur_province_id) {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"> -- Select -- </option>';
			}

			$("#" + province_id).html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$('#shipping_country_id').change(function(e) {
	var zone = $('#shipping_zone_id');
	var cur_province_id = zone.data("zone_id");
	zone.data("zone_id", null);
	var country_id = $('#shipping_country_id').val();
	change_country('shipping_zone_id', country_id, cur_province_id);
	if (country_id == 44) {
		$('#china_div').show();
	} else {
		$('#china_div').hide();
	}
});

$('#country_id').change(function(e) {
	var country_id = $('#country_id').val();
	change_country('zone_id', country_id);
});

$('input[name=firstname],input[name=company],input[name=city],select[name=zone_id],input[name=address_1],input[name=postcode],select[name=country_id],input[name=email],input[name=phone]').on('change', function() {
	$('input[name=address_id]').val(0);
});

$('input[name=chinaid_back],input[name=chinaid_front],input[name=chinaid], input[name=shipping_firstname],input[name=shipping_company],input[name=shipping_city],select[name=shipping_zone_id],input[name=shipping_address_1],input[name=shipping_postcode],select[name=shipping_country_id],input[name=shipping_email],input[name=shipping_phone]').on('change', function() {
	$('input[name=shipping_address_id]').val(0);
	var shipping_list = $('#select_shipping_address');
	if (shipping_list.is(":visible")) {
		shipping_list.val(0);
	}
});

var formverify = 1;

$(document).ready(function() {
	$('#formproduct').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) {
			e.preventDefault();
			return false;
		}
	});

	$('#formproduct').on('submit', function(e) {
		if (formverify) {
			e.preventDefault();
			$.ajax({
				url: 'index.php?route=product/apply/verify',
				type: "POST",
				data: $("#formproduct").serialize(),
				dataType: 'json',
				success: function(json) {
					if (json.status == 'OK') {
						formverify = 0;
						$('#formproduct').submit();
					} else {
						html  = '<strong>Warning! </strong>';
						html += json.err_message;
						$('.warningtxt').html(html).css("display", "");
						$("html,body").animate({scrollTop:$("#warning_box").offset().top},400)
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
			return false;
		}
	});
});
//--></script>
<?php if (isset($shipping_selected)) { ?>
	<script>
		// 复制的订单，选择收件人地址 + 填充商品
		$(document).ready(function() {
			$("#select_shipping_address").find("option").each(function() {
				if ($(this).text() == "<?php echo $shipping_selected; ?>") {
					$(this).attr("selected", "selected").parent().trigger("change");
				}
			});

            var products = [];
            var isFirstTime = true;
            <?php
                foreach ($products as $product) {
					echo "products.push(['" . $product['upc'] . "', " . $product['quantity'] . "]);\n";
				}
			?>
            $.each(products, function(key, val) {
				if (!isFirstTime) {
				    $(".addmore").click();
                } else {
                    isFirstTime = false;
                }
                var item = $(".product_item:last");
				item.find('.upc').val(val[0]).data("ui-autocomplete")._trigger('change'); // 触发jquery的autocompletechange的必要写法
				item.find('.quantity').val(val[1]);
			});
		});
	</script>
<?php } ?>
<?php echo $footer; ?>
