<?php
function pageHeader() {
	global $config;
	/*
	if((intval($_SESSION['userId']) <= 0) && !in_array($config['currentFile'], $config['publicFiles']) ) {
		redirect($config['siteURL']);
	}
	if((intval($_SESSION['userId']) > 0) && ($_SESSION['userType'] == 'automat')) {
		if(!in_array($config['currentFile'], $config['automatUserLoginFiles'])) {
			redirect($config['siteURL']);
		}		
	}
	*/
print '<?xml version="1.0" encoding="UTF-8"?> ';
?>
<!DOCTYPE html>
<html lang="ro">
<head>
	<meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
    <title><?php echo(strip_tags(getPageText(1))); ?></title>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,400italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="css/MonoSocialIconsFont/MonoSocialIconsFont.css" type="text/css"/>
    
    <link rel="stylesheet" href="openlayers/theme/default/style.css" type="text/css"/>
    
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/960.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
   
	<link rel="stylesheet" href="css/jquery-ui-timepicker-addon.css"/ >
    <link rel="stylesheet" href="css/style.css" />
    
    
    
    <script src="js/jquery-2.0.3.min.js"></script>
    <script src="js/jquery-migrate-1.2.1.min.js"></script>
    <script src="js/jquery-ui.js"></script>
    <script src="js/datepicker-ro.js"></script>    
       
	<script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript" src="openlayers/lib/OpenLayers.js"></script>
    <script type="text/javascript" src="js/script.js"></script>   
    <?php $bodyExtraAttb = ''; ?>
    <?php if($config['currentMenuSection'] == 'index') { $bodyExtraAttb = ' onload="initIndex()"';  } ?>   
    <?php if($config['currentMenuSection'] == 'adauga') { $bodyExtraAttb = ' onload="initAdauga()"';  } ?>
    <?php if($config['currentMenuSection'] == 'contact') { $bodyExtraAttb = ' onload="initContact()"';  } ?>
    
    
	<?php if($config['currentSubMenuSection'] == 'sesizare'): ?>    
		<script type="text/javascript">var switchTo5x=true;</script>
        <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
        <script type="text/javascript">stLight.options({publisher: "13fbc8e5-d04b-41fa-abc1-c531c26449f4", doNotHash: true, doNotCopy: true, hashAddressBar: false});</script>    
	<?php endif; ?>    
    
</head>
<body<?php echo($bodyExtraAttb); ?>>
<?php if($config['currentSubMenuSection'] == 'sesizare'): ?>
    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=1611300099100141&version=v2.0";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<?php endif; ?>

<div id="wrap">
<div id="main">
    <div id="headerMenuBackground" role="complementary">
        <div class="container_12">
          <div class="grid_9">
            <div id="logo" title="Prima pagina"><a href="./" title="">Harta Intaractiva - Prima pagina</a></div>
            <div class="clear"></div>
            <div role="navigation">
                <ul id="navMenu" role="menubar">
                    <li<?php if($config['currentMenuSection'] == 'index') { print(' class="on"'); }  ?>><a href="./">Rapoarte</a></li>
                    <li<?php if($config['currentMenuSection'] == 'adauga') { print(' class="on"'); }  ?>><a href="adauga.php">Adauga Sesizare</a></li>
                    <li<?php if($config['currentMenuSection'] == 'alerte') { print(' class="on"'); }  ?>><a href="alerte.php">Alerte</a></li>
                    <li<?php if($config['currentMenuSection'] == 'contact') { print(' class="on"'); }  ?>><a href="contact.php">Contact</a></li>
                </ul>
            </div>
          </div>
          <div class="grid_3">
                <div class="socialWrapper">
                        <a href="#" class="symbol" id="socialPinterest">&#xe264;</a>
                        <a href="#" class="symbol" id="socialInstagram">&#xe300;</a>
                        <a href="#" class="symbol" id="socialTwitter">&#xe287;</a>
                        <a href="#" class="symbol" id="socialGoogleplus">&#xe239;</a>
                        <a href="#" class="symbol" id="socialYoutube">&#xe299;</a>
                        <a href="#" class="symbol" id="socialFacebook">&#xe227;</a>
                </div>
                <div class="projectPartners">
                    <div class="projectPartnersImages">
                        <a href="http://www.activewatch.ro/" target="_blank"><div class="activewatch" title="Activewatch"></div></a>
                    </div>
                    <div class="projectPartnersText" title="Un proiect realizat cu sprijinul Activewatch">Un proiect </div>
                </div>
          </div>      
          <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
    
    <div class="container_12 separator"></div>
    <div class="clear"></div>
    
        <?php
        queueParamMessage();
        printMessages();
}


function pageFooter() {
	global $config;	
	?>
    
    <div class="container_12">
      <div class="grid_12 finantare">
        ActiveWatch este o organizație de drepturile omului care militează pentru comunicare liberă în interes public. Programul FreeEx al ActiveWatch beneficiază de o finanțare în valoare de 74 702 euro prin granturile SEE 2009 – 2014, în cadrul Fondului ONG în România (<a href="http://www.fondong.fdsc.ro/" target="_blank">www.fondong.fdsc.ro</a>), și de 8 400 de dolari din partea IFEX pentru proiectul “Harta Interactivă a Libertății de Exprimare” (implementat în perioada mai 2014 – iunie 2015). <br>
        <a href="http://www.ifex.org/" target="_blank" title="Website IFEX"><div class="logo_ifex"></div></a>        
        <a href="http://www.fondong.fdsc.ro/" target="_blank" title="Website Fondul O.N.G."><div class="logo_fondul_ong"></div></a>
        <a href="http://www.eeagrants.org/" target="_blank" title="Website granturile SEE şi norvegiene"><div class="logo_eea"></div></a>
        Pentru informații oficiale despre granturile SEE şi norvegiene accesați <a href="http://www.eeagrants.org/" target="_blank">www.eeagrants.org</a>. <br>
        Pentru mai multe informații despre rețeaua IFEX accesați <a href="http://www.ifex.org/" target="_blank">www.ifex.org</a>. <br>
        Conținutul acestui material nu reprezintă în mod necesar poziția oficială a granturilor SEE 2009-2014 sau a IFEX.<br><br>      	
        <div class="donatii"><?php echo (''. getPageText(6) ) ; ?></div>
      </div>
      
      <div class="clear"></div>
    </div>
    <div class="clear"></div>    
    
    
</div>
<!-- END <div id="main"> -->

    
</div>
<!-- END <div id="wrap"> -->


<footer role="complementary">
    <div class="container_12">
      <div class="clear"></div>
      <div class="grid_9">
      	<a<?php if($config['currentMenuSection'] == 'index') { print(' class="on"'); }  ?> href="./">Rapoarte</a> | 
        <a<?php if($config['currentMenuSection'] == 'adauga') { print(' class="on"'); }  ?> href="adauga.php">Adauga o Sesizare</a> | 
        <a<?php if($config['currentMenuSection'] == 'alerte') { print(' class="on"'); }  ?> href="alerte.php">Alerte</a> | 
        <a<?php if($config['currentMenuSection'] == 'contact') { print(' class="on"'); }  ?> href="contact.php">Contact</a>
        <br><br><?php echo(strip_tags(getPageText(2),'<br>')); ?><br>
      </div>
      <div class="grid_3">
        	<div class="footerSocialWrapper">
            		<a href="#" class="symbol" id="socialPinterest">&#xe264;</a>
                    <a href="#" class="symbol" id="socialInstagram">&#xe300;</a>
                    <a href="#" class="symbol" id="socialTwitter">&#xe287;</a>
                    <a href="#" class="symbol" id="socialGoogleplus">&#xe239;</a>
                    <a href="#" class="symbol" id="socialYoutube">&#xe299;</a>
                    <a href="#" class="symbol" id="socialFacebook">&#xe227;</a>
            </div>      	
      </div> 
      <div class="clear"></div>     
	</div>
</footer>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1172111-43', 'auto');
  ga('send', 'pageview');

</script>

</body>
</html>   
    <?php
}
