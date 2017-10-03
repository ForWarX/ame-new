<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
</head>
<body>
<div class="container">
    <h1><?php echo $title; ?></h1>
    <div class="alert alert-warning"><?php echo empty($error) ? '' : $error; ?></div>
    <div class="alert alert-success"><?php echo empty($message) ? '' : $message; ?></div>
    <form id='uploadform' action='<?php echo $action_url; ?>' method='POST'  enctype="multipart/form-data">
	    <div class="row">
	    	<div class="col-sm-4">Customer ID : </div>
	    	<div class="col-sm-4"><input type='text' name='customer_id' value='<?php echo $customer_id; ?>'></div>
	    </div>
	    <div class="row">
	    	<div class="col-sm-4">Upload File : </div>
	    	<div class="col-sm-4"><input type='file' name='file' value=''></div>
	    </div>
	    <div class="row">
	    	<div class="col-sm-4"></div>
	    	<div class="col-sm-4"><input type='submit' value='Upload' id='uploadBtn'></div>
	    </div>
    </form>
</div>
<script>
$( document ).ready(function() {
	$('.alert-warning').hide();
	$('.alert-success').hide();

	$('#uploadform').submit( function(e) {
		e.preventDefault();
		$('#uploadBtn').hide();
		$('.alert-warning').hide();
		$('.alert-success').hide();
		var data = new FormData(this); // <-- 'this' is your form element
		$.ajax({
			url: '<?php echo $action_url; ?>',
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			timeout: 600000,	// 10 mintes 
			type: 'POST',
			dataType: 'json',
			error: function(jqXHR, textStatus, errorThrown) {
				$('#uploadBtn').show();

				if(textStatus==="timeout") {
					alert("File is too big to process. Please ask admin for double check"); //Handle the timeout
				} else {
					alert("Somthing is wrong your file may cause some system error. Please contact admin"); //Handle other error type
				}
			},
			success: function(rt) {
				$('#uploadBtn').show();
				if (rt.error) {
					$('.alert-warning').html(rt.errormsg);
					$('.alert-warning').show();
				} else if (rt.message) {
					$('.alert-success').html(rt.message);
					$('.alert-success').show();
				}
			},
		});
	});
});
</script>
</body>
</html>