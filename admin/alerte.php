<?php
	// activ:
	$configArray['sesizareType'][0]['name'] = 'Inactiv';
	$configArray['sesizareType'][1]['name'] = 'Activ';

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	$configArray['currentModule'] = 'alerte';

	pageHeader();

	$_GET['q'] = htmlspecialchars(trim($_GET['q']));
	if(strlen($_GET['q'])) $_tableDesc['queryString'] = '?q='.urlencode($_GET['q']); else $_tableDesc['queryString'] = '?';
	
	if($_GET['cmd'] == 'activ') {
		if(intval($_GET['alerta_id'])) {
			$inregistrareActivStatus = getQueryInArray("SELECT validated FROM ".$configArray['DBname'].".alerte WHERE alerta_id = ".intval($_GET['alerta_id'])." LIMIT 1");
			if(count($inregistrareActivStatus)) {
				switch ($inregistrareActivStatus[0]['validated']) { 
					case 0: $newStatus = 1; break;
					case 1: $newStatus = 0; break;
					default: echo 'activ status error.'; exit();
				}
				updateTable("UPDATE ".$configArray['DBname'].".alerte SET validated = '".$newStatus."' WHERE alerta_id = ".intval($_GET['alerta_id']).""); redirect(remove_querystring_var(remove_querystring_var($_SERVER['REQUEST_URI'],'alerta_id'),'cmd')); 
			}
		}		
	}	
	
	
	
	$_tableDesc['detaliiURL'] = 'alerte_edit.php';
	$_tableDesc['title'] = 'Alerte';
	$_tableDesc['info'] = '';
	$_tableDesc['adaugaCerereURL'] = 'alerte_add.php';
	$_tableDesc['columns'] = array('ID' => 'alerta_id', 
								   'Nume' => 'alerta_nume', 
								   'E-mail' => 'alerta_email', 
								   'Adaugat la' => 'alerta_added_at', 
								   'Detalii' => '______');
	$_tableDesc['columnsNotReal'] = array('______');	
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();	
	//SELECT
	$QRY = 'SELECT *
			FROM alerte
			WHERE 1 = 1 
			';
   //WHERE
	if(strlen($_GET['q'])) {
		$_GET['q'] = mysql_escape_string($_GET['q']);
		$QRY .= " AND (";
			$k = 0;
			foreach ($_tableDesc['columns'] as $key => $col) {
				if(!in_array($col,$_tableDesc['columnsNotReal'])) {
					//echo($col . " | ");
					if($col == 'id') $colTbl = 'c.'; 
					else $colTbl = '';
					$QRY .= $colTbl."".$col." LIKE '%".$_GET['q']."%'";
					if($k != count($_tableDesc['columns']) - count($_tableDesc['columnsNotReal']) - 1) $QRY .= " OR ";
				}
				$k++;				
			}
		$QRY .= " )";
	}
	
	//ORDER
	if(isset($_GET['ord']) && in_array($_GET['ord'],$_tableDesc['columns']))  { 
		$_tableDesc['orderColumn'] = $_GET['ord']; 
		$_GET['way'] = strtoupper($_GET['way']);
		if(($_GET['way'] == "ASC") || ($_GET['way'] == "DESC")) $_tableDesc['orderColumnWay'] = $_GET['way'];
		else $_tableDesc['orderColumnWay'] = 'DESC'; 
	} else { 
		$_tableDesc['orderColumn'] = 'alerta_id'; //default order
		$_tableDesc['orderColumnWay'] = 'DESC'; 
	}
	if($_tableDesc['orderColumn'] == "ip") { $_tableDesc['orderColumn'] = "sip"; }
	if($_tableDesc['orderColumn'] == "activ") { $_tableDesc['orderColumn'] = "c.activ"; }
	$QRY .= " ORDER BY ".$_tableDesc['orderColumn']." ".$_tableDesc['orderColumnWay']."";
	
	//echo '['.$_tableDesc['orderColumn'].' - '.$_tableDesc['orderColumnWay'].']';
	//QRY
	//print $QRY;
	$_tableData = getQueryInArray($QRY);
	$_tableDesc['totalRecordsInTable'] = count($_tableData);
?>	
    
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td class="w100"><h1><?php echo($_tableDesc['title']); ?></h1></td>
                        	<td>
                            	<div id="cautaBox">
                                	<form method="get" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
		                            	Cauta rapid: <input type="text" name="q" value="<?php echo($_GET['q']); ?>" /> <input class="submit" type="submit" name="submit_q" value="Go" />
                                    </form>
                                </div>
                             </td>
                        </tr>
                        <tr><td colspan="2"><!-- <div class="addCerere"><a href="<?php echo($_tableDesc['adaugaCerereURL']); ?>" class="addCerere"><img align="left" src="images/add_cont.png" width="28" height="28" border="0" alt="Adauga inregistrare" class="addCerere" />Adauga</a></div> --></td></tr>
                        </table>

                    <div class="container">
	                    <?php if(strlen($_tableDesc['info'])) { print('Informatii aditionale: '.$_tableDesc['info']); } 
							  echo('<div id="pageNavLinks"> Total: <strong>'.$_tableDesc['totalRecordsInTable'].' inregistrari</strong>. </div> ');
						?>
                        <table cellpadding="0" cellspacing="0" class="T">
                        	<tr class="th">
                            <?php
								$k = 0;
								foreach ($_tableDesc['columns'] as $key => $col) {
									if($_tableDesc['orderColumn'] == $col) { 
										if($_tableDesc['orderColumnWay'] == "DESC") $currentWay = "ASC"; else $currentWay = "DESC"; 
									} else { 
										$currentWay = "ASC"; 
									}
									if(in_array($col,$_tableDesc['columnsNotReal'])) $colURL = '#'; else $colURL = $_tableDesc['queryString'].'&ord='.$col.'&way='.$currentWay.'';
									print '<th'.($_tableDesc['orderColumn'] == $col?' class="on"':'').'><div><a href="'.$colURL.'">'.$key.'</a><!--<a class="order" href="#"><img src="images/b_updown.gif" width="22" height="24" border="0" /></a>--></div></th>'."\n";
								 	$k++;
								}//endforeach
							?>
                            </tr>
                            <?php
								for($i=0;$i<count($_tableData);$i++) {
									print '<tr class="trTd_'.($_tableData[$i]['activ']).'" onmouseover="this.className=\'trTdOver\';" onmouseout="this.className=\'trTd_'.($_tableData[$i]['activ']).'\';">'."\n";		
									$k = 0;
									// afisare linie
									foreach ($_tableDesc['columns'] as $key => $col) {
										if($col == 'sesizare_titlu') { print '<td>'.$_tableData[$i][$col].'</td>'."\n"; }
										elseif($col == 'validated') { print '<td align="center"><a title="'.$configArray['sesizareType'][$_tableData[$i]['validated']]['name'].'" href="?'.$_SERVER['QUERY_STRING'].'&cmd=activ&alerta_id='.$_tableData[$i]['alerta_id'].'">'.getImage('activ_'.intval($_tableData[$i]['validated']).'.png', $configArray['sesizareType'][$_tableData[$i]['validated']]['name']).'</a></td>'."\n"; 	}										
										elseif($col == '______') { print '<td><a href="'.$_tableDesc['detaliiURL'].'?id='.$_tableData[$i]['alerta_id'].'">modifica</a></td>'."\n"; }
										else { print '<td>'.$_tableData[$i][$col].'</td>'."\n"; } //default 
										$k++;
									}//endforeach coloane
									print '</tr>'."\n";
								}
							?>                                                                                                                                                                                                                             
                        </table>
                    </div>
                    
                </div>         

<?php
	pageFooter();
?>