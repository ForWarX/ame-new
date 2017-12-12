<div id="slideshow<?php echo $module; ?>" class="owl-carousel" style="opacity: 1;">
  <?php foreach ($banners as $banner) { ?>
  <div class="item">
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" class="img-responsive" />
    <?php } ?>
  </div>
  <?php } ?>
</div>
<script type="text/javascript"><!--
$('#slideshow<?php echo $module; ?>').owlCarousel({
	items: 6,
	autoPlay: 3000,
	singleItem: true,
	navigation: true,
	navigationText: ['<i class="fa fa-chevron-left fa-5x"></i>', '<i class="fa fa-chevron-right fa-5x"></i>'],
	pagination: true
});
--></script>

<!--首页订单查询-->
<!--
<div id="track">
  <form id="order_track">
    <div class="form-group">
      <label for="OrderNO"><?php echo $text_track_label; ?></label>
      <input type="text" class="form-control input-lg" id="ame_no" name="ame_no" placeholder="<?php echo $text_track_placeholder; ?>">
    </div>
    <button type="submit" class="btn btn-primary btn-lg" id="OrderTrackBTN">
      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> <a href="<?php echo 'index.php?route=information/track'; ?>"   ><?php echo $text_track_btn; ?></a>
    </button>
  </form>
  <div id="track_ad" class="col-sm-12 hidden-xs">
    <p><?php echo $text_track_ad1; ?></p>
    <p><?php echo $text_track_ad2; ?> <span style="color:rgb(44, 196,238);"><?php echo $text_track_ad3; ?></span></p>
    <p><?php echo $text_track_ad4; ?></p>
  </div>
</div>
-->

<!-- 首页中间四个按钮：服务流程、联系客服、网上下单、会员中心 -->
<div id="home_mid_btns" class="container text-center">
  <div class="row">
    <div class="col-sm-3 col-xs-6">
      <a href="">
        <div>
          <img src="<?php echo HTTP_SERVER . '/image/catalog/default/home/service.png'; ?>" alt="<?php echo $text_home_mid_btn_service; ?>" class="img-responsive">
        </div>
        <p><?php echo $text_home_mid_btn_service; ?></p>
      </a>
    </div>
    <div class="col-sm-3 col-xs-6">
      <a href="index.php?route=information/contact">
        <div>
          <img src="<?php echo HTTP_SERVER . '/image/catalog/default/home/contact.png'; ?>" alt="<?php echo $text_home_mid_btn_contact; ?>" class="img-responsive">
        </div>
        <p><?php echo $text_home_mid_btn_contact; ?></p>
      </a>
    </div>
    <div class="col-sm-3 col-xs-6">
      <a href="index.php?route=product/apply">
        <div>
          <img src="<?php echo HTTP_SERVER . '/image/catalog/default/home/online_order.png'; ?>" alt="<?php echo $text_home_mid_btn_order; ?>" class="img-responsive">
        </div>
        <p><?php echo $text_home_mid_btn_order; ?></p>
      </a>
    </div>
    <div class="col-sm-3 col-xs-6">
      <a href="index.php?route=account/account">
        <div>
          <img src="<?php echo HTTP_SERVER . '/image/catalog/default/home/member_center.png'; ?>" alt="<?php echo $text_home_mid_btn_member; ?>" class="img-responsive">
        </div>
        <p><?php echo $text_home_mid_btn_member; ?></p>
      </a>
    </div>
  </div>
</div>


<script>
    $('#OrderTrackBTN').bind('click', function() {
        url = 'index.php?route=information/track';

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

<!--
<script>
      $("#OrderTrackBTN").click(function () {
          $.ajax({
              async: false, // 防止window.open被拦截
              url: 'index.php?route=account/order/get_order_track',
              type: "POST",
              data: $("#order_track").serialize(),
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
-->