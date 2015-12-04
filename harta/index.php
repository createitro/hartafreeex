<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>FreeEx - Harta Interactiva</title>
    <link rel="stylesheet" href="reset.css" />
    <link rel="stylesheet" href="960.css" />
</head>
<body style="background-image:url(bg.jpg); background-repeat:repeat-x;">
	<div align="center">
    	<div style="width:1020px"><img src="header.jpg" width="1020" height="150" usemap="#Map" />
          <map name="Map">
            <area shape="rect" coords="39,117,127,152" href="?p=index">
            <area shape="rect" coords="146,121,295,161" href="?p=adauga">
            <area shape="rect" coords="312,118,376,154" href="?p=alerte">
            <area shape="rect" coords="394,121,472,171" href="?p=contact">
            <area shape="rect" coords="38,3,346,92" href="?p=index">
          </map>
    	</div>
        <div style="width:1020px">
        	
        	<?php 
				error_reporting (0);
				$p = $_GET['p'];
				if(!strlen($p)) { $p = 'index'; }
				$default = '<img src="index.jpg" usemap="#Map2" /><map name="Map2"><area shape="rect" coords="281,477,983,825" href="?p=detalii"></map>';
				
				switch ($p) {
					case 'index':
						print($default); break;
					case 'adauga': 
						print '<img src="'.$p.'.jpg" />'; break;
					case 'alerte': 
						print '<img src="'.$p.'.jpg" />'; break;
					case 'contact': 
						print '<img src="'.$p.'.jpg" />'; break;
					case 'detalii': 
						print '<img src="'.$p.'.jpg" />'; break;		
					default:
					  print($default);
				}
			?>
        </div>
        <div style="width:1020px"><img src="footer.jpg" width="1020" height="95" /></div>
	</div>
</body>
</html>
