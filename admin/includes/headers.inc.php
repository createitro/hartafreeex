<?php
function pageHeader() {
	global $configArray;
	global $_tableDesc;
	verifyLogin();
	
	
	//if($_SESSION['userIdConturi'] == 129) {
		if (isset($_SESSION['userId']) && $_SESSION['userId'] >= 0) {
			//get rights for current module
			$getCurrentModuleRights = getQueryInArray("SELECT * FROM mm_cont_modul cm JOIN modules m ON m.module_id = cm.id_modul WHERE m.module_slug = '".$configArray['currentModule']."' AND cm.id_cont = ".$_SESSION['userId']." LIMIT 1");
			if(count($getCurrentModuleRights)) {
				$configArray['rightRead'] = intval($getCurrentModuleRights[0]['r']);
				$configArray['rightWrite'] = intval($getCurrentModuleRights[0]['w']);
			} else {
				$configArray['rightRead'] = 0;
				$configArray['rightWrite'] = 0;
			}
			//redirect 
			//echo($configArray['currentModule'].': '.$configArray['rightRead'].' '.$configArray['rightWrite']);
			if(!$configArray['rightRead'] && !stristr(strtolower($_SERVER['SCRIPT_NAME']),'index.php') ) { redirect('index.php?msg=7'); }
			

		}//endif logged
		
//	}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
	<meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
    <title>Harta FreeEx Admin</title>
    <link href="css/reset.css" rel="stylesheet" type="text/css" />
    <link href="css/style.css" rel="stylesheet" type="text/css" />
    <link href="css/menu.css" rel="stylesheet" type="text/css" />
	<?php if(!$configArray['noHeader']): ?>    
    <script type="text/javascript" src="js/menu.js"></script>      
	<script type="text/javascript" language="javascript" src="js/lytebox.js"></script>        
    <link rel="stylesheet" href="css/lytebox.css" type="text/css" media="screen" /> 
    <link rel="stylesheet" href="openlayers/theme/default/style.css" type="text/css"/>
    
	<?php endif; //noheader ?>                
    <?php if($configArray['prototypeOn']): ?>
		<script type="text/javascript" language="javascript" src="js/prototype.js"></script>
        <script type="text/javascript" language="javascript" src="js/scriptaculous.js"></script>
        <script type="text/javascript" language="javascript" src="js/effects.js"></script>
        <script type="text/javascript" language="javascript" src="js/controls.js"></script>    
    <?php else: ?>
		<?php /*?><script type="text/javascript" src="js/jquery-1.2.3.js"></script><?php */?>
        <script src="//code.jquery.com/jquery-2.0.3.min.js"></script>

		<script src="3rdparty/ckeditor/ckeditor.js"></script>
        <script src="3rdparty/ckeditor/adapters/jquery.js"></script>

	    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	    <link rel="stylesheet" href="css/jquery-ui-timepicker-addon.css"/ >
		<script src="js/jquery-ui.js"></script>
        <script src="js/datepicker-ro.js"></script>   
	    <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
                
	    <script type="text/javascript" src="openlayers/lib/OpenLayers.js"></script>        
        
        <script type="text/javascript" src="js/tooltip1.js"></script>  
           
    <?php endif; ?>    
    <style type="text/css"> img, div { behavior: url(css/iepngfix.htc) } </style>     
    <script type="text/javascript" src="js/iepngfix_tilebg.js"></script> 
    <script type="text/javascript" src="js/script.js?r=<?php echo(rand(0,1000000)); ?>"></script>
    <link rel="stylesheet" type="text/css" href="js/jscal2/css/jscal2.css" />
    <link rel="stylesheet" type="text/css" href="js/jscal2/css/border-radius.css" />
    <script type="text/javascript" src="js/jscal2/js/jscal2.js"></script>
    <script type="text/javascript" src="js/jscal2/js/lang/ro.js"></script>

    <?php $bodyExtraAttb = ''; ?>    
    <?php if($configArray['currentMenuSection'] == 'sesizare_add') { $bodyExtraAttb = ' onload="initAdauga()"';  } ?>
    <?php if($configArray['currentMenuSection'] == 'sesizare_edit') { $bodyExtraAttb = ' onload="initEdit()"';  } ?>     
</head>
<body<?php echo($bodyExtraAttb); ?>>
<?php if(!$configArray['noHeader']): ?>
<table cellpadding="0" cellspacing="0" id="topTable">
	<tr>
    	<td valign="top" align="left" id="logoBox" class="w100"><a href="./" onmouseover="changeImages('logoancs', 'images/logo-over.gif'); return true;" onmouseout="changeImages('logoancs', 'images/logo.gif'); return true;" onmousedown="changeImages('logoancs', 'images/logo-over.gif'); return true;" onmouseup="changeImages('logoancs', 'images/logo-over.gif'); return true;"><img name="logoancs" src="images/logo.gif" width="275" height="59" border="0" alt="" /></a></td>
        <td valign="top" align="left" id="authBox">
        	<div class="authDiv">
        	<?php 
			//login area
			printLoginArea();
			?>
            </div>
        </td>
    </tr>
    <tr>
   	  <td align="left" valign="top" class="w100" colspan="2" id="menuTD">
        <div id="ancs_menu">
           <?php generateMenuNew(); ?>
        </div>      
      </td>
    </tr>
    <tr>
    	<td align="left" valign="top" class="w100" colspan="2">
            <div id="mainWrapper">            
<?php endif; //noheader ?>        
			<?php getSiteMsg($_GET['msg']); ?>
<?php
}

function pageFooter() {
	global $configArray;
?>
<?php if(!$configArray['noHeader']): ?>            
            </div>    
	    </td>
    </tr>
</table>
<?php endif; //noheader ?>   
<script language="javascript" type="text/javascript"> initDhtmlGoodiesMenu(); </script>
</body>
</html>
<?php
}
?>