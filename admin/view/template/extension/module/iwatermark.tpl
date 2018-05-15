<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
          <h1><i class="fa fa-copyright"></i>&nbsp;<?php echo $heading_title_dashboard; ?></h1>
          <ul class="breadcrumb">
            <?php foreach ($breadcrumbs as $breadcrumb) { ?>
            <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
            <?php } ?>
          </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php echo (empty($data['iwatermark']['LicensedOn'])) ? base64_decode('ICAgIDxkaXYgY2xhc3M9ImFsZXJ0IGFsZXJ0LWRhbmdlciBmYWRlIGluIj4NCiAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJjbG9zZSIgZGF0YS1kaXNtaXNzPSJhbGVydCIgYXJpYS1oaWRkZW49InRydWUiPsOXPC9idXR0b24+DQogICAgICAgIDxoND5XYXJuaW5nISBVbmxpY2Vuc2VkIHZlcnNpb24gb2YgdGhlIG1vZHVsZSE8L2g0Pg0KICAgICAgICA8cD5Zb3UgYXJlIHJ1bm5pbmcgYW4gdW5saWNlbnNlZCB2ZXJzaW9uIG9mIHRoaXMgbW9kdWxlISBZb3UgbmVlZCB0byBlbnRlciB5b3VyIGxpY2Vuc2UgY29kZSB0byBlbnN1cmUgcHJvcGVyIGZ1bmN0aW9uaW5nLCBhY2Nlc3MgdG8gc3VwcG9ydCBhbmQgdXBkYXRlcy48L3A+PGRpdiBzdHlsZT0iaGVpZ2h0OjVweDsiPjwvZGl2Pg0KICAgICAgICA8YSBjbGFzcz0iYnRuIGJ0bi1kYW5nZXIiIGhyZWY9ImphdmFzY3JpcHQ6dm9pZCgwKSIgb25jbGljaz0iJCgnYVtocmVmPSNzdXBwb3J0XScpLnRyaWdnZXIoJ2NsaWNrJykiPkVudGVyIHlvdXIgbGljZW5zZSBjb2RlPC9hPg0KICAgIDwvZGl2Pg==') : '' ?>
        
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger autoSlideUp"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
             <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success autoSlideUp"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <script>$('.autoSlideUp').delay(3000).fadeOut(600, function(){ $(this).show().css({'visibility':'hidden'}); }).slideUp(600);</script>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="storeSwitcherWidget">
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="fa fa-pushpin"></span>&nbsp;<?php echo $store['name']; if($store['store_id'] == 0) echo " <strong>(".$text_default.")</strong>"; ?>&nbsp;<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach ($stores  as $st) { ?>
                            <li><a href="index.php?route=<?php echo $module_path ?>&store_id=<?php echo $st['store_id'];?>&token=<?php echo $token; ?>"><?php echo $st['name']; ?></a></li>
                        <?php } ?> 
                    </ul>
                </div>
                <h3 class="panel-title"><i class="fa fa-list"></i>&nbsp;<span style="vertical-align:middle;font-weight:bold;">Module settings</span></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form"> 
                    <input type="hidden" name="store_id" value="<?php echo $store['store_id']; ?>" />
                    <input type="hidden" name="iwatermark_status" value="1" />
                    <div class="tabbable">
                        <div class="tab-navigation form-inline">
                            <ul class="nav nav-tabs mainMenuTabs" id="mainTabs">
                                <li class="active"><a href="#control_panel" data-toggle="tab"><i class="fa fa-power-off"></i>&nbsp;Control Panel</a></li>
                                <li><a href="#support" data-toggle="tab"><i class="fa fa-ticket"></i>&nbsp;Support</a></li>
                            </ul>
                            <div class="tab-buttons">
                                <span id="submit_button_container">
                                    <button type="submit" class="btn btn-success save-changes"><i class="fa fa-check"></i>&nbsp;Save Changes</button>
                                    <a href="<?php echo $cancel; ?>" class="btn btn-warning">Cancel</a>
                                </span>
                                
                                <span id="clean_working_container" style="display: none;">
                                    <i class="fa fa-spin circle-o-notch"></i> <?php echo $text_clean; ?>
                                    <button id="clean_stop" class="btn btn-danger"><?php echo $text_stop; ?></button>
                                </span>
                            </div> 
                        </div><!-- /.tab-navigation --> 
                        <div class="tab-content">
                            <div id="control_panel" class="tab-pane active"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_control_panel.php'); ?></div>
                            <div id="support" class="tab-pane"><?php require_once(DIR_APPLICATION.'view/template/'.$module_path.'/tab_support.php'); ?></div>
                        </div> <!-- /.tab-content --> 
                    </div><!-- /.tabbable -->
                    <input type="hidden" class="selectedTab" name="selectedTab" value="<?php echo (empty($this->request->get['tab'])) ? 0 : $this->request->get['tab'] ?>" />
                    <input type="hidden" class="selectedStore" name="selectedStore" value="<?php echo (empty($this->request->get['store'])) ? 0 : $this->request->get['store'] ?>" />
                </form>
            </div> 
        </div>
    </div>
</div>
<script type="text/javascript">
    $('#mainTabs a:first').tab('show'); // Select first tab
    if (window.localStorage && window.localStorage['currentTab']) {
        $('.mainMenuTabs a[href="'+window.localStorage['currentTab']+'"]').tab('show');
    }
    if (window.localStorage && window.localStorage['currentSubTab']) {
        $('a[href="'+window.localStorage['currentSubTab']+'"]').tab('show');
    }
    $('.fadeInOnLoad').css('visibility','visible');
    $('.mainMenuTabs a[data-toggle="tab"]').click(function() {
        if (window.localStorage) {
            window.localStorage['currentTab'] = $(this).attr('href');
        }
    });
    $('a[data-toggle="tab"]:not(.mainMenuTabs a[data-toggle="tab"], .review_tabs a[data-toggle="tab"])').click(function() {
        if (window.localStorage) {
            window.localStorage['currentSubTab'] = $(this).attr('href');
        }
    });
</script>
<script type="text/javascript">
    var clean = (function($) {
        var 
            xhr = null,
            selector = {
                form_buttons: '#submit_button_container',
                progress: '#clean_working_container'
            },
            init = function() {
                $(selector.form_buttons).hide();
                $(selector.progress).show();
                work();
            },
            finish = function() {
                $(selector.form_buttons).show();
                $(selector.progress).hide();
            },
            work = function() {
                xhr = $.ajax({
                    url: '<?php echo $clean_url_work; ?>',
                    dataType: 'json',
                    success: function(data) {
                        if (!data.done) {
                            work();
                        } else {
                            finish();
                        }
                    }
                });
            },
            cancel = function() {
                if (xhr) xhr.abort();

                $.ajax({
                    url: '<?php echo $clean_url_cancel; ?>',
                    complete: finish
                });
            };

        $('#clean_stop').click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            cancel();
        });

        return {
            init: init
        }
    })(jQuery);

    // Start cleaning - this fires only if there is a cleaning process initialized in the session. Every step of the cleaning process operates on only a single product_id group: 1-1000, 1001-2000, 2001-3000, etc.

    <?php if ($clean) : ?>
        clean.init();
    <?php endif; ?>
</script>
<?php echo $footer; ?>