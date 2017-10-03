<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <h3><?php echo $message; ?></h1>
    </div>
  </div>
  <script type="text/javascript"><!--
  setTimeout(loadpage, 3000);

  function loadpage() {
	location = '<?php echo str_replace('&amp;', '&', $url); ?>';
  };
	//--></script> 
</div>
<?php echo $footer; ?>