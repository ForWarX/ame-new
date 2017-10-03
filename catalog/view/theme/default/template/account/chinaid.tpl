<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php } ?>
    <?php if ($error_warning) { ?>
    <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <h2><?php echo $text_chinaid; ?></h2>
            <?php if ($addresses) { ?>
                <form action="" method="post">
                    <div class="form-group">
                        <label for="name"><?php echo $text_name; ?></label>
                        <input type="text" class="form-control" id="name" placeholder="<?php echo $text_name; ?>">
                    </div>
                    <div class="form-group">
                        <label for="chinaid"><?php echo $text_chinaid; ?></label>
                        <input type="text" name="chinaid" class="form-control" id="chinaid" placeholder="<?php echo $text_chinaid; ?>">
                    </div>
                    <div class="form-group">
                        <label for="id_front"><?php echo $text_china_id_front; ?></label>
                        <input type="file" id="id_front" name="chinaid_front">
                    </div>
                    <div class="form-group">
                        <label for="id_back"><?php echo $text_china_id_back; ?></label>
                        <input type="file" id="id_back" name="chinaid_back">
                    </div>
                    <input type="hidden" name="address_id[]" class="address_id">
                    <button type="submit" class="btn btn-primary"><?php echo $button_upload; ?></button>
                </form>
            <?php } else { ?>
                <p><?php echo $text_empty; ?></p>
            <?php } ?>
            <div class="buttons clearfix">
                <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
                <div class="pull-right"><a href="<?php echo $add; ?>" class="btn btn-primary"><?php echo $button_new_address; ?></a></div>
            </div>
            <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
</div>
<script>
    var availableTags = [
        <?php
        foreach ($addresses as $id => $val) {
            echo '"' . $id . '",';
        }
        ?>
    ];

    var china_ids = {};
    <?php
    foreach ($addresses as $id => $val) {
        $output = "china_ids['" . $id . "']=['" . $val['chinaid_front'] . "','" . $val['chinaid_back'] . "',[";
        foreach ($val['address_id'] as $addr) {
            $output .= '"' . $addr . '",';
        }
        $output .= "]];\n";
        echo $output;
    }
    ?>

    $("#chinaid").autocomplete({ source: availableTags, select: get_id, change: get_id });

    function get_id(e, u) {
        e.preventDefault();
        var inputval = u.item ? u.item.value : $(this).val();
        if (inputval.length > 0) {
            $(this).val(inputval);
            var addrs = china_ids[inputval][2];
            var addr_input = $(".address_id");
            var form = addr_input.parent();
            var addr_input_clone = $(".address_id:first").clone();
            addr_input.remove();
            addrs.forEach(function(val, index) {
                var new_input = addr_input_clone.clone().val(val);
                form.append(new_input);
            });
        }
    }
</script>

<?php echo $footer; ?>