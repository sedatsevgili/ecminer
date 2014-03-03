<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
    	<meta content="text/html; charset=utf-8" http-equiv="Content-Type"></meta>
    	<script type="text/javascript" src="<?php echo PATH;?>js/jquery-1.4.2.js"></script>
    	<script type="text/javascript" src="<?php echo PATH;?>js/jquery.validate.js"></script>
    	<link href="<?php echo PATH;?>css/reset.css" rel="stylesheet" type="text/css" />
    	<link href="<?php echo PATH;?>css/admin.css" rel="stylesheet" type="text/css" />
    	<script type="text/javascript">
    	$(document).ready(function() {
    	$("ul#topnav li").hover(function() { //Hover over event on list item
    		$(this).css({ 'background' : '#1376c9 url(<?php echo PATH;?>images/topnav_active.gif) repeat-x'}); //Add background color + image on hovered list item
    		$(this).find("span").show(); //Show the subnav
    	} , function() { //on hover out...
    		$(this).css({ 'background' : 'none'}); //Ditch the background
    		$(this).find("span").hide(); //Hide the subnav
    	});
    	});

    	function ajaxTable(tableId,order,limit) {
			$('#loadingTable_'+tableId).show();
			$.ajax({
				type: 'GET',
				url: '<?php echo PATH;?>static/ajax.php',
				data: 'do=getTable&tableId='+tableId+'&order='+order+'&limit='+limit,
				success: function(msg) {
					$('#'+tableId+'_wrapper').html($.parseJSON(msg));
				},
				complete: function(xhr,status) {
					$('#loadingTable_'+tableId).hide();
				}
			});
    	}

    	function ajaxDeleteRow(tableId,tables,rowId,deleteModel) {
			if(!confirm('Satırı silmek istediğinizden emin misiniz?')) {
				return false;
			}
			$('#loadingTable_'+tableId).show();
			$.ajax({
				type: 'GET',
				url: '<?php echo PATH;?>static/ajax.php',
				data: 'do=deleteRow&tables='+tables+'&rowId='+rowId+'&deleteModel='+deleteModel,
				success: function(msg) {
					if(msg == '1') {
						$('#row_'+rowId).remove();
					} else {
						alert(msg);
					}
				},
				complete: function(xhr,status) {
					$('#loadingTable_'+tableId).hide();
				}
			});
    	}
    	</script>
    </head>
    <body>
		<?php 
		require_once(PATH."controller/Menu.php");
		$menu = new Menu(PATH."admin/menu.xml");
		$menu->run();
		?>
    	<div style="clear:both; position: absolute; top: 85px; display: block; width: 970px;">
