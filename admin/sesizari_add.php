<?php

	// include server.inc.php
	$fi = "includes/server.inc.php";
	if (file_exists($fi)) { require_once($fi); } else { echo('Nu am putut include un fisier.'); exit(); }

	
	$fi = "fckeditor/fckeditor.php";
	if (file_exists($fi))  { include("$fi"); }
	else { echo("Cannot include a file."); exit(); }
		
	$sBasePath = $_SERVER['PHP_SELF'] ;
	$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, 'sesizari' ) ) ;
	$sBasePath .= "fckeditor/";

	$configArray['currentModule'] = 'sesizari';

	
	$configArray['currentMenuSection'] = 'sesizare_add';
	
	pageHeader();
	
	//DB SELECT FROM STRUCT
	intranetDBConnect();

	
	
?>	
                <div class="subTable">
						<table cellpadding="0" cellspacing="0" class="T-top">
                        <tr>
                        	<td class="w100"><h1><img src="images/userpic_default.jpg" width="48" height="48" border="0" align="left" style="margin-right:10px;" />Adauga Sesizare</h1>

                            <div class="Dcontainer">
                            	<br />
                                <?php
								
								$submitOK = true;
								if (!isset($_POST['submit']) || $_POST['submit'] == ''){ 							
									$submitOK = false;
									$data_ora = date("Y-m-d H:00");
									//Sesizare Categorii
									$selected_categs = array();

								} else {
									$sesizare_titlu = trim($_POST['sesizare_titlu']);
									if(strlen($sesizare_titlu) < 1) { $error['sesizare_titlu'] = 'Obligatoriu.'; $submitOK = false; } 
									
									$sesizare_descriere = trim($_POST['sesizare_descriere']);
									if(strlen($sesizare_descriere) < 1) { $error['sesizare_descriere'] = 'Obligatoriu.'; $submitOK = false; } 
									
									$data_ora = trim($_POST['data_ora']);
									if(strlen($data_ora) < 1) { $error['data_ora'] = 'Obligatoriu.'; $submitOK = false; } 									
									
									//Sesizare Categorii
									$selected_categs = array();
									foreach ($_POST as $key => $value) {
									  //just checkboxes
									  if(begins_with($key,'cb')) { $selected_categs[] = $value;  }
									}	
									if(!count($selected_categs)) { $error['selected_categs'] = 'Cel putin o categorie.'; $submitOK = false; } 			

									//Sesizare Localizare Coordonate
									$coord_lon = floatval($_POST['coord_lon']);
									$coord_lat = floatval($_POST['coord_lat']);
									
									$location_search = filter_var($_POST['location_search'], FILTER_SANITIZE_STRING);
									
									//get location reverse geocode (via Google)
									$location_reverse = getReverseGeocode($coord_lat,$coord_lon);									
									

									//Sesizare Linkuri Sursa
									$linkuri_sursa = $_POST['linkuri_sursa'];		
									
									//Sesizare Coduri Embed
									$embed_sursa = $_POST['embed_sursa'];																	
									
									//personal info
									$personal_nume = trim($_POST['personal_nume']);
									$personal_prenume = trim($_POST['personal_prenume']);
									$personal_email = trim($_POST['personal_email']);
									$personal_telefon = trim($_POST['personal_telefon']);

								} // endif post submit								
								
								
								if ($submitOK && $configArray['rightWrite']) {
										
										$_LOG_STR = date("Y-m-d H:i:s").' '.$_SESSION['username'].' a adaugat sesizarea<br /> '."\n";		
										
										$insertSesizareStr = "INSERT INTO sesizari SET ".
											"sesizare_titlu = '". mysql_escape_string($sesizare_titlu)."', ".
											"sesizare_descriere = '". ($sesizare_descriere)."', ".
											"data_ora = '". mysql_escape_string($data_ora)."', ".
											"coord_lon = '". mysql_escape_string($coord_lon)."', ".
											"coord_lat = '". mysql_escape_string($coord_lat)."', ".
											"location_search = '". mysql_escape_string($location_search)."', ".
											"location_reverse = '". mysql_escape_string($location_reverse)."', ".
											//"personal_nume = '". mysql_escape_string($personal_nume)."', ".
											//"personal_prenume = '". mysql_escape_string($personal_prenume)."', ".
											//"personal_email = '". mysql_escape_string($personal_email)."', ".
											//"personal_telefon = '". mysql_escape_string($personal_telefon)."', ".
											"added_by_user_id = ".intval($_SESSION['userId']).",".
											"change_log = CONCAT(change_log, '".$_LOG_STR."'), ".
											"added_at = '". date("Y-m-d H:i:s")."' ".
											" ";
										//echo $insertSesizareStr;
										$insertErr = 0;
										if(!mysql_query($insertSesizareStr, $configArray['dbcnx'])) { 
											$insertErr = 1; echo ' eroare la insert 1: ['.mysql_error().']'; 
											die();
										} else { 
											$_ID = mysql_insert_id($configArray['dbcnx']);
										}
										
										//insert $selected_categs
										deleteFieldWhere('mm_categs_sesizari', 'sesizare_id', $_ID);
										for($i=0;$i<count($selected_categs);$i++) {
											insertIntoTable("INSERT INTO mm_categs_sesizari SET sesizare_id = ".$_ID.", categ_id = ".intval($selected_categs[$i])." ");
										}//endfor $selected_categs												
										
										
										//insert $linkuri_sursa
										deleteFieldWhere('mm_sesizari_linkuri', 'sesizare_id', $_ID);
										for($i=0;$i<count($linkuri_sursa);$i++) {
											if(strlen(trim($linkuri_sursa[$i]))) {
												insertIntoTable("INSERT INTO mm_sesizari_linkuri SET sesizare_id = ".$_ID.", link_sursa = '".$linkuri_sursa[$i]."' " );
											}
										}//endfor $linkuri_sursa	
										
										
										//insert $embed_sursa
										deleteFieldWhere('mm_sesizari_embeds', 'sesizare_id', $_ID);
										for($i=0;$i<count($embed_sursa);$i++) {
											if(strlen(trim($embed_sursa[$i]))) {
												insertIntoTable("INSERT INTO mm_sesizari_embeds SET sesizare_id = ".$_ID.", embed_sursa = '".$embed_sursa[$i]."' " );
											}
										}//endfor $embed_sursa																				
										
												
										
										//////////////////////////////////////////////////////////////////////////////////////////////////////////										
										
										$insertLog = "INSERT INTO log SET ".
											"data = NOW(), ".
											"obs = 'ADAUGARE SESIZARE - DUPA AUTENTIFICARE', ".
											"ip = '".get_ip_address()."', ".
											"query = '". mysql_real_escape_string( $insertSesizareStr )."' ".
											"";								
										if(!mysql_query($insertLog, $configArray['dbcnx'])) { $insertErr = 1; echo $insertLog.' &nbsp; eroare la adaugare 2'; } else { echo ''; }
										
										if(!$insertErr) { echo '<br />Adaugarea fost facuta cu succes!<br /><br />'; }
										redirect('sesizari.php?msg=5');
										
								} else {
								?>
                                    <form method="post" id="sendForm" enctype="multipart/form-data" action="<?php echo($_SERVER['REQUEST_URI']); ?>">
                                        <input type="hidden" name="frefererf" value="<?php echo($_SERVER['HTTP_REFERER']); ?>" />                                     
                                        <table cellpadding="2" cellspacing="2" border="0" class="w100">
	                                        <tr>
                                            	<td align="left" valign="top">Titlu Sesizare*:<?php if(isset($error['sesizare_titlu'])) { echo('<span class="error blink"><br /> '.$error['sesizare_titlu'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="sesizare_titlu" value="<?php echo(htmlspecialchars($sesizare_titlu)); ?>" style="width:300px;" /></td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top">Descriere*:<?php if(isset($error['sesizare_descriere'])) { echo('<span class="error blink"><br /> '.$error['sesizare_descriere'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="error" style="width:100%;">
                                                <textarea style="width:700px; height:300px;" name="sesizare_descriere" id="sesizare_descriere"><?php echo($sesizare_descriere); ?></textarea>
												<script> 
                                                    var roxyFileman = '<?php echo($configArray['siteURL']); ?>3rdparty/fileman/index.html'; 
                                                    CKEDITOR.replace('sesizare_descriere', {filebrowserBrowseUrl:roxyFileman, filebrowserImageBrowseUrl:roxyFileman+'?type=image', removeDialogTabs: 'link:upload;image:upload'} ); 
                                                </script>                                                 </td>
                                            </tr>
	                                        <tr>
                                            	<td align="left" valign="top">Data si Ora*:<?php if(isset($error['data_ora'])) { echo('<span class="error blink"><br /> '.$error['data_ora'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="error" style="width:100%;">
                                                <input class="date_adauga" name="data_ora" id="data_ora" type="text" value="<?php echo(htmlspecialchars($data_ora)); ?>" />
                                                <script> $('#data_ora').datetimepicker(/*{ maxDate: 0 }*/);  </script>
                                                </td>
                                            </tr>     
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>                                            

                                            
                                            <tr>
                                            	<td align="left" valign="top">Categorii*:<?php if(isset($error['selected_categs'])) { echo('<span class="error blink"><br /> '.$error['selected_categs'].'</span>'); } ?></td>
                                                <td align="left" valign="top" class="" style="width:100%;">
                                                	<div class="formBoxInput" style="width:500px;">
                                                    
													<?php
                                                        generateCategoriesCheckboxes($selected_categs);
                                                    ?>
                                                    </div>
                                                </td>
                                            </tr>                                       
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>
                                            
                                            <tr>
                                            	<td align="left" valign="top">Localizare*:</td>
                                                <td align="left" valign="top" class="" style="width:100%;">
                                                	<div id="map" class="mapAdauga"></div>
                                                    <div class="clear"></div>
                                                    <br /><input name="location_search" id="location_search" type="text" placeholder="Caută adresa..." value="" />
                                                    <div id="search_location_btn" class="search_location_btn"></div>
                                                    <input name="coord_lon" id="coord_lon" type="hidden" value="" />
                                                    <input name="coord_lat" id="coord_lat" type="hidden" value="" />                                                    
                                                </td>
                                            </tr>                                             
                                            
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>                                            
                                            
                                            <tr>
                                            	<td align="left" valign="top">Linkuri Sursa:</td>
                                                <td align="left" valign="top" class="" style="width:100%;">
                                                	<div class="formBoxInput" style="width:500px;">  
                                                    	<?php
														for($i=0;$i<count($_LINKURI_SESIZARE);$i++) {
															$linkURL = $_LINKURI_SESIZARE[$i]['link_sursa'];														
															print '<input name="linkuri_sursa[]" id="link_sursa_'.$i.'" type="text" class="link_sursa" value="'.htmlspecialchars($linkURL).'" />'."\n";
														}
														if(!count($_LINKURI_SESIZARE)) { print '<input name="linkuri_sursa[]" id="link_sursa_0" type="text" class="link_sursa" value="" />'."\n"; }
														?>                                      
                                                        <div id="add_link" title="Adauga inca un link catre sursa"></div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </td>
                                            </tr>                                       
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>  
                                            
                                            

                                            <tr>
                                            	<td align="left" valign="top">Video Embed:</td>
                                                <td align="left" valign="top" class="" style="width:100%;">
                                                	<div class="formBoxInput" style="width:500px;">  
                                                    	<?php
														for($i=0;$i<count($_CODURI_EMBED);$i++) {
															$codEmbed = $_CODURI_EMBED[$i]['embed_sursa'];								
															print ' <input name="embed_sursa[]" id="embed_sursa_'.$i.'" type="text" class="embed_sursa" value="'.htmlspecialchars($codEmbed).'" />'."\n";
														}
														if(!count($_CODURI_EMBED)) { print ' <input name="embed_sursa[]" id="embed_sursa_0" type="text" class="embed_sursa" value="" />'."\n"; }
														?>                                      
                                                        <div id="add_embed" title="Adauga cod embed video"></div>
                                                        <div class="clear"></div>
                                                    </div>
                                                </td>
                                            </tr>                                       
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>                                                                                        
                                            
                                            
                                            
                                             
                                            <!--
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>  
                                             
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>                                               
	                                        <tr>
                                            	<td align="left" valign="top">Nume:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="personal_nume" value="<?php echo(htmlspecialchars($personal_nume)); ?>" style="width:300px;" /><?php if(isset($error['personal_nume'])) { echo('&nbsp; '.$error['personal_nume']); } ?></td>
                                            </tr>   
	                                        <tr>
                                            	<td align="left" valign="top">Prenume:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="personal_prenume" value="<?php echo(htmlspecialchars($personal_prenume)); ?>" style="width:300px;" /><?php if(isset($error['personal_prenume'])) { echo('&nbsp; '.$error['personal_prenume']); } ?></td>
                                            </tr> 
	                                        <tr>
                                            	<td align="left" valign="top">E-mail:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="personal_email" value="<?php echo(htmlspecialchars($personal_email)); ?>" style="width:300px;" /><?php if(isset($error['personal_email'])) { echo('&nbsp; '.$error['personal_email']); } ?></td>
                                            </tr>                                                                                                                                                                                                        
	                                        <tr>
                                            	<td align="left" valign="top">Telefon:</td>
                                                <td align="left" valign="top" class="error" style="width:100%;"><input type="text" name="personal_telefon" value="<?php echo(htmlspecialchars($personal_telefon)); ?>" style="width:300px;" /><?php if(isset($error['personal_telefon'])) { echo('&nbsp; '.$error['personal_telefon']); } ?></td>
                                            </tr>  
                                            <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="20" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                            </tr>
                                            -->                                                                                                                                                                                                                                                                                                                                         
                                                                                                                                                                                                                          	                                   
	                                        <tr>
                                            	<td align="left" valign="top"><img src="images/spacer.gif" width="200" height="1" border="0" alt="" /></td>
                                                <td align="left" valign="top" class="error"></td>
                                             </tr>                                                                                          
	                                        <tr>
                                            	<td align="left" valign="top"></td>
                                            	<td align="left" valign="top">
		                                        <input type="submit" name="submit" class="submitBtn" value="ADAUGA" />
                                            	</td>
                                             </tr>
                                          </table>
                                    </form>
									<?php
                                        if(!$configArray['rightWrite']) {
                                            ?>
                                            <script language="javascript" type="text/javascript">
                                                $(':input').attr('disabled', true);	
                                            </script>
                                            <?php
                                        }
                                    ?>                                      
                                    <br /><br /><br />
                                <?php 	}//endif submit ?>
								<a href="sesizari.php">&laquo; inapoi </a>
								<br /><br /><br />
                            </div>
                            
                            </td>
                        </tr>
                        </table>
                                           
                </div>     
                


    <script type="text/javascript">
        var map, layer, vectorLayer, featureMarker;
		var coord_lon = 0;
		var coord_lat = 0;
		
        function initAdauga() {
            OpenLayers.ProxyHost = "<?php echo($config['siteURL']); ?>openlayers/examples/proxy.cgi?url=";
            map = new OpenLayers.Map('map', {
                controls: [
                    new OpenLayers.Control.PanZoom(),
                    new OpenLayers.Control.MousePosition(),
                    new OpenLayers.Control.Navigation()
                ],
				projection: new OpenLayers.Projection("EPSG:900913"), 
				displayProjection: new OpenLayers.Projection("EPSG: 4326")
            });
			
            layer = new OpenLayers.Layer.OSM("OpenStreetMap", null, { transitionEffect: 'resize' });
			/*
			layer = new OpenLayers.Layer.OSM("Stamen toner", 
								   ["http://tile.stamen.com/toner/${z}/${x}/${y}.png"], 
									{transitionEffect: 'resize', attribution: "&copy; <a href='http://www.openstreetmap.org/'>OpenStreetMap</a> and contributors, under an <a href='http://www.openstreetmap.org/copyright' title='ODbL'>open license</a>. Toner style by <a href='http://stamen.com'>Stamen Design</a>",
									"tileOptions": { "crossOriginKeyword": null }} );
			*/
									
            map.addLayers([layer]);
			
			//projection (for transform)
			var epsg4326 =  new OpenLayers.Projection("EPSG:4326"); //WGS 1984 projection
			var projectTo = map.getProjectionObject(); //The map projection (Spherical Mercator)			
			
			//init (Bucharest)
			var marker_choose = [{'ID':'marker_choose', 'LATITUDE':44.4378258, 'LONGITUDE':26.0946376}];
			coord_lon = marker_choose[0].LONGITUDE;
			coord_lat = marker_choose[0].LATITUDE;
			
			var marker_choose_point = new OpenLayers.Geometry.Point(marker_choose[0].LONGITUDE, marker_choose[0].LATITUDE);
			//var LonLat = new OpenLayers.LonLat( marker_choose[0].LONGITUDE, marker_choose[0].LATITUDE).transform(epsg4326, projectTo);
			
			//vector
			vectorLayer = new OpenLayers.Layer.Vector("Vector Layer");
			
			
			//Make the feature a plain OpenLayers marker
			featureMarker = new OpenLayers.Feature.Vector(
					marker_choose_point.transform(epsg4326, projectTo),
					{description:'This is the value of<br>the description attribute'} ,
					{externalGraphic: './openlayers/img/marker.png', graphicHeight: 25, graphicWidth: 21, graphicXOffset:-18, graphicYOffset:-0  }
				);
		 
			vectorLayer.addFeatures(featureMarker);
			
			//drag		
			var drag = new OpenLayers.Control.DragFeature(vectorLayer, {
				autoActivate: true,
				onComplete: function(feature) { 
					var point = new OpenLayers.Geometry.Point(feature.geometry.x, feature.geometry.y).transform(projectTo, epsg4326);
					coord_lon = point.x;
					coord_lat = point.y;
					//alert(coord_lon + ' ' + coord_lat);
				}
			});
			map.addControl(drag);
			
			map.addLayer(vectorLayer);
			
			//center map default [Romania]
			var positionRomaniaDefault       = new OpenLayers.LonLat(25.0094303, 45.9442858);
			map.setCenter(positionRomaniaDefault.transform(epsg4326, projectTo), 6);
			
		
        }
		
		
        function submitSearchLocation(queryString) {
            //var queryString = document.forms[0].query.value;
			var PostDataXML = '<xls:XLS xmlns:xls="http://www.opengis.net/xls" xmlns:sch="http://www.ascc.net/xml/schematron" xmlns:gml="http://www.opengis.net/gml" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/xls http://schemas.opengis.net/ols/1.1.0/LocationUtilityService.xsd" version="1.1"> <xls:RequestHeader/> '
							+ '<xls:Request methodName="GeocodeRequest" requestID="123456789" version="1.1"> <xls:GeocodeRequest> '			
							+ '<xls:Address countryCode="RO"> <xls:freeFormAddress>' + encodeURIComponent(queryString + ', Romania') + '</xls:freeFormAddress> </xls:Address> </xls:GeocodeRequest> </xls:Request> </xls:XLS> ';
			
			$("#search_location_btn").removeClass("search_location_btn").addClass("loader_21");
            OpenLayers.Request.POST({
                /* url: "http://www.openrouteservice.org/php/OpenLSLUS_Geocode.php", */
				url: "http://openls.geog.uni-heidelberg.de/testing2015/geocoding",
                scope: this,
                failure: this.requestFailure,
                success: this.requestSuccess,
                headers: {"Content-Type": "text/xml;charset=utf-8"},
                data: PostDataXML
            });
        }
        function requestSuccess(response) {
			$("#search_location_btn").removeClass("loader_21").addClass("search_location_btn");
            var format = new OpenLayers.Format.XLS();
            var output = format.read(response.responseXML);
			console.log(output);
            if (output.responseLists[0]) {
                var geometry = output.responseLists[0].features[0].geometry;
                var foundPosition = new OpenLayers.LonLat(geometry.x, geometry.y).transform( new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject() );
                map.setCenter(foundPosition, 16);
				featureMarker.move(foundPosition);
				coord_lon = geometry.x;
				coord_lat = geometry.y;				
				//alert(coord_lon + ' ' + coord_lat);
            } else {
                alert("Nu am gasit nici o adresa. Va rugam incercati altceva.");
            }
        }
        function requestFailure(response) {
			console.log(response);
            alert("Eroare de comunicare cu serviciul OpenLS. Va rugam incercati din nou. ");
        }
    </script>                     

<?php

	pageFooter();
?>