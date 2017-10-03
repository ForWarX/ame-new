<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-box-type" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-box-type" class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-box-num"><?php echo $entry_box_num; ?></label>
                        <div class="col-sm-10">
                            <input type="number" name="box_type_num" value="<?php echo $box_type_num; ?>" placeholder="<?php echo $entry_box_num; ?>" id="input-box-num" class="form-control" />
                        </div>
                        <input type="hidden" name="box_type" id="input-box-data"><!-- 最终上传的box_type -->
                        <?php if (empty($box_type)) { ?>
                        <div class="input-box-type">
                            <label class="col-sm-2 control-label" for="input-box-type"><?php echo $entry_box_type; ?></label>
                            <div class="col-sm-4">
                                <input type="text" placeholder="<?php echo $entry_box_name; ?>" class="form-control input-box-name" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" placeholder="<?php echo $entry_box_length; ?>" class="form-control input-box-length" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" placeholder="<?php echo $entry_box_width; ?>" class="form-control input-box-width" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" placeholder="<?php echo $entry_box_height; ?>" class="form-control input-box-height" />
                            </div>
                        </div>
                        <?php } else { ?>
                        <?php foreach($box_type as $name=>$size) { ?>
                        <div class="input-box-type">
                            <label class="col-sm-2 control-label" for="input-box-type"><?php echo $entry_box_type; ?></label>
                            <div class="col-sm-4">
                                <input type="text" value="<?php echo $name; ?>" placeholder="<?php echo $entry_box_name; ?>" class="form-control input-box-name" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" value="<?php echo $size['length']; ?>" placeholder="<?php echo $entry_box_length; ?>" class="form-control input-box-length" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" value="<?php echo $size['width']; ?>" placeholder="<?php echo $entry_box_width; ?>" class="form-control input-box-width" />
                            </div>
                            <div class="col-sm-2">
                                <input type="number" value="<?php echo $size['height']; ?>" placeholder="<?php echo $entry_box_height; ?>" class="form-control input-box-height" />
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="box_type_status" id="input-status" class="form-control">
                                <?php if ($box_type_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="box_type_sort_order" value="<?php echo $box_type_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // 提交表单
    $("#form-box-type").on("submit", function () {
        var box = $(".input-box-type");
        var input = $("#input-box-data");
        var data = {};
        box.each(function() {
            var type =$(this);
            var name = type.find(".input-box-name").val();
            var width = Number(type.find(".input-box-width").val());
            var length = Number(type.find(".input-box-length").val());
            var height = Number(type.find(".input-box-height").val());
            if (name.length > 0 && width > 0 && length > 0 && height > 0) {
                data[name] = {};
                data[name]['width'] = width;
                data[name]['length'] = length;
                data[name]['height'] = height;
            }
        });
        input.val(JSON.stringify(data));

        return true;
    });

    // 改变盒子个数
    $("#input-box-num").on("change", function() {
        var num = Number($(this).val());
        if (num <= 0) {
            $(this).val(0);
            $(".input-box-type:gt(0)").remove();
            $(".input-box-type").find("input").val("");
        } else {
            var box = $(".input-box-type");
            if (box.length > num) {
                $(".input-box-type:gt(" + (num-1) + ")").remove();
            } else {
                var first_box = $(".input-box-type:first");
                for(var i = num-box.length; i > 0; i--) {
                    var clone = first_box.clone();
                    clone.find("input").val("");
                    $(".input-box-type:last").after(clone);
                }
            }
        }
    });
</script>
<?php echo $footer; ?>