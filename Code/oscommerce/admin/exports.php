<?php
require('includes/application_top.php');
require(DIR_WS_FUNCTIONS . 'exports.php');
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">EXPORTS</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <?php 
        $exportItem = "";
        $exportType = "";
        $exportField = "";
        $exportDirectory = "";
        if(isset($_GET["exportItem"])) {
        	$exportItem = $_GET["exportItem"];
        }
        if(isset($_GET["exportType"])) {
        	$exportType = $_GET["exportType"];
        }
        if(isset($_GET["exportField"])) {
        	$exportField = $_GET["exportField"];
        }
        if(isset($_GET["exportDirectory"])) {
        	$exportDirectory = str_replace("/","",$_GET["exportDirectory"]);
        	$exportDirectory = str_replace(".","",$exportDirectory);
        	$exportDirectory = str_replace("\\","",$exportDirectory);
        }
        if(!empty($exportItem) && !empty($exportType) && !empty($exportField) && !empty($exportDirectory)) {
        	$exportResult = runExport($exportItem,$exportType,$exportField,$exportDirectory);
        	if(!$exportResult) {
        		showError("Export failure");
        	} else {
        		showMessage("Exported file is <a href='".$exportResult."' target='_blank'>here</a>");
        	}
        }
        ?>
        <form name="exportForm" action="exports.php?exportItem=<?php echo $exportItem;?>&exportType=<?php echo $exportType;?>&exportField=<?php echo $exportField?>" method="GET">
        	<center><table border="0" width="100%" cellspacing="0" cellpadding="0">
        		<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
        			<td class="dataTableContent" align="center" width="20%">Please choose an export item: </td>
        			<td class="dataTableContent" align="left" width="80%">
        			<select name="exportItem" id="exportItem" onchange="javascript:document.exportForm.submit();">
        				<option value="0">--Choose--</option>
        				<option value="catalog" <?php if($exportItem == "catalog") { echo "selected"; }?>>Catalog</option>
        				<option value="customer" <?php if($exportItem == "customer") { echo "selected"; }?>>Customer</option>
        				<option value="order" <?php if($exportItem == "order") { echo "selected"; }?>>Order</option>
        			</select>
        			</td>
        		</tr>
        		<?php 
        		if(!empty($exportItem)) {
					switch($exportItem) {
						case "catalog":
							showCatalogSubForm($exportField);
							break;
						case "customer":
							showCustomerSubForm($exportField);
							break;
						case "order":
							showOrderSubForm($exportField);
							break;
						default:
							showError("Please choose an export item");
							break;
					}
        			if(!empty($exportField)) {
        				?>
        				<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
		        			<td class="dataTableContent" align="center" width="20%">Please choose an export type: </td>
		        			<td class="dataTableContent" align="left" width="80%">
		        			<select name="exportType" id="exportType">
		        				<option value="0">--Choose--</option>
		        				<option value="xml" <?php if($exportType == "xml") { echo "selected"; }?>>Xml</option>
		        				<option value="excel" <?php if($exportType == "excel") { echo "selected"; }?>>Excel</option>
		        			</select>
		        			</td>
		        		</tr>
		        		<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">
		        			<td class="dataTableContent" align="center" width="20%">Export Directory: </td>
		        			<td class="dataTableContent" align="left" width="80%">
		        			<input type="text" name="exportDirectory" id="exportDirectory" value="<?php echo $exportDirectory; ?>"/>
		        			</td>
		        		</tr>
		        		<tr>
		        			<td class="dataTableContent" align="left" colspan="2"><input type="Submit" value="Export" /></td>
		        		</tr>
        				<?php 
        			}
        		}
        		?>
        	</table></center>
        </form>
        </td>
     </tr>
    </table>
   </td>
  </tr>
 </table>
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>