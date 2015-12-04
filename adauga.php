<?php
	require_once('includes/config.php');	
	
	require_once('classes/wideimage/WideImage.php');	

	//Process Send Document Form
	$sendAllSubmitOK = 1;	
	

	if(strlen($_POST['btn_adauga_submit'])) {
		
		if(strlen($_POST['email-title']) > 0) {
			die('Spam!');
		}

		//Sesizare Titlu
		$titlu = filter_var($_POST['titlu'], FILTER_SANITIZE_STRING);
		if(!strlen($titlu)) { $formErr['titlu'] = 'Va rugam completati titlul sesizarii!'; $sendAllSubmitOK = 0;	}		
		
		//Sesizare Descriere
		$descriere = filter_var($_POST['descriere'], FILTER_SANITIZE_STRING);
		if(!strlen($descriere)) { $formErr['descriere'] = 'Va rugam completati titlul sesizarii!'; $sendAllSubmitOK = 0;	}
		
		//Sesizare Data si Ora
		$data_ora = filter_var($_POST['data_ora'], FILTER_SANITIZE_STRING);
		if(!strlen($data_ora)) { $formErr['data_ora'] = 'Va rugam completati data si ora!'; $sendAllSubmitOK = 0;	}		
		
		//Sesizare Categorii
		$selected_categs = array();
		foreach ($_POST as $key => $value) {
		  //just checkboxes
		  if(begins_with($key,'cb')) { $selected_categs[] = $value;  }
	    }		
		//print_nice($selected_categs); exit();
		
		//Sesizare Localizare Coordonate
		$coord_lon = floatval($_POST['coord_lon']);
		$coord_lat = floatval($_POST['coord_lat']);
		
		$location_search = filter_var($_POST['location_search'], FILTER_SANITIZE_STRING);
		
		//Sesizare Linkuri Sursa
		$linkuri_sursa = $_POST['linkuri_sursa'];
		
		//Sesizare Coduri Embed
		$embed_sursa = $_POST['embed_sursa'];		
		
		
		//Sesizare Informatii Optionale
		$nume = filter_var($_POST['nume'], FILTER_SANITIZE_STRING);
		$prenume = filter_var($_POST['prenume'], FILTER_SANITIZE_STRING);
		$email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
		$telefon = filter_var($_POST['telefon'], FILTER_SANITIZE_STRING);
						
		//Process Insert Data
		if ($sendAllSubmitOK):
		
			//Uploaded Files Check
			$nrUserUploads = count( $_FILES["file_input"]['tmp_name']);
			//echo $nrUserUploads.'<br />'; print_nice($_FILES["file_input"]);  var_dump(is_uploaded_file( $_FILES["file_input"]['tmp_name'][2]));
			
			$nrValidFilesUploads = 0;
			$validFilesUploadsArray = array(); //save to this array
			
			for($i=0;$i<$nrUserUploads;$i++) {
				$file_name_from_disk = $_FILES['file_input']['name'][$i];
				$_EXTENSION = strtolower(getFileExtension($file_name_from_disk));
				$ext = '.'.$_EXTENSION;
				$titlu_and_filename = str_replace($ext, '', $titlu.'_'.$file_name_from_disk);
				
				if ( is_uploaded_file( $_FILES["file_input"]['tmp_name'][$i] ) && in_array($_EXTENSION, array('jpg','gif','png'))) {
					//valid upload
					$validFilesUploadsArray[$nrValidFilesUploads]['tmp'] =  $_FILES["file_input"]['tmp_name'][$i];
					//copy
					$original_input_path = $config['filesUploadDir'] . prepareFileName($titlu_and_filename, '_original') . $ext;
					$moved = move_uploaded_file($_FILES["file_input"]['tmp_name'][$i], $original_input_path);
					if(!$moved) { die('Error uploading file!');  }							
					//main
					$file_input = prepareFileName($titlu_and_filename, '') . $ext;
					$validFilesUploadsArray[$nrValidFilesUploads]['file_input'] =  $file_input;
					$file_input_path = $config['filesUploadDir'] . $file_input;
					WideImage::load($original_input_path)->crop('center', 'center', $config['sesizare']['imgBig']['w'], $config['sesizare']['imgBig']['h'])->saveToFile($file_input_path);
					//thumb
					$file_input_thumb = prepareFileName($titlu_and_filename, '_thumb') . $ext;
					$validFilesUploadsArray[$nrValidFilesUploads]['file_input_thumb'] =  $file_input_thumb;
					$file_input_thumb_path = $config['filesUploadDir'] . $file_input_thumb;
					WideImage::load($original_input_path)->crop('center', 'center', $config['sesizare']['imgThumb']['w'], $config['sesizare']['imgThumb']['h'])->saveToFile($file_input_thumb_path);
					//delete
					unlink($original_input_path);
					
					//increment valid number
					$nrValidFilesUploads++;
				} //endif valid upload
			} //endfor nrUserUploads
			
			if(count($validFilesUploadsArray)) {
				$file_input = $validFilesUploadsArray[0]['file_input'];
				$file_input_thumb = $validFilesUploadsArray[0]['file_input_thumb'];
			} else {
				$file_input = $file_input_thumb = '';
			}
		
			
			//get location reverse geocode (via Google)
			$location_reverse = getReverseGeocode($coord_lat,$coord_lon);
			
			//generate random validation code
			$validation_code = create_random_string(32);
			
			//Insert Sesizare							
			insertIntoTable("INSERT INTO sesizari SET 
							 sesizare_titlu = :sesizare_titlu, 
							 sesizare_descriere = :sesizare_descriere,
							 data_ora = :data_ora,
							 coord_lon = :coord_lon,
							 coord_lat = :coord_lat,
							 location_search = :location_search,
							 location_reverse = :location_reverse,
							 file_input = :file_input,
							 file_input_thumb = :file_input_thumb,
							 personal_nume = :personal_nume,
							 personal_prenume = :personal_prenume,
							 personal_email = :personal_email,
							 personal_telefon = :personal_telefon,
							 added_at = :added_at,
							 validation_code = :validation_code
							 ", array(
								"sesizare_titlu" => $titlu,
								"sesizare_descriere" => nl2br($descriere), 
								"data_ora" => $data_ora,
								"coord_lon" => $coord_lon,
								"coord_lat" => $coord_lat,
								"location_search" => $location_search,
								"location_reverse" => $location_reverse,
								"file_input" => $file_input,
								"file_input_thumb" => $file_input_thumb,
								"personal_nume" => $nume,
								"personal_prenume" => $prenume,
								"personal_email" => $email,
								"personal_telefon" => $telefon,
								"added_at" => date("Y-m-d H:i:s"),		
								//"added_by_ip" => getIP(),
								"validation_code" => $validation_code												
							 ));
			$sesizareId = $config['dbConnection']->lastInsertId();

			//insert $validFilesUploadsArray[
			for($i=0;$i<count($validFilesUploadsArray);$i++) {
				insertIntoTable("INSERT INTO mm_sesizari_images SET sesizare_id = :sesizare_id, file_input = :file_input, file_input_thumb = :file_input_thumb ", 
				array("sesizare_id" => $sesizareId, "file_input" => $validFilesUploadsArray[$i]['file_input'], "file_input_thumb" => $validFilesUploadsArray[$i]['file_input_thumb'] ));
			}//endfor $validFilesUploadsArray
			
			
			//insert $linkuri_sursa
			for($i=0;$i<count($linkuri_sursa);$i++) {
				if(strlen($linkuri_sursa[$i])) {
					insertIntoTable("INSERT INTO mm_sesizari_linkuri SET sesizare_id = :sesizare_id, link_sursa = :link_sursa ", 
					array("sesizare_id" => $sesizareId, "link_sursa" => $linkuri_sursa[$i] ));
				}
			}//endfor $linkuri_sursa
			
			
			//insert $embed_sursa
			for($i=0;$i<count($embed_sursa);$i++) {
				if(strlen($embed_sursa[$i])) {
					insertIntoTable("INSERT INTO mm_sesizari_embeds SET sesizare_id = :sesizare_id, embed_sursa = :embed_sursa ", 
					array("sesizare_id" => $sesizareId, "embed_sursa" => $embed_sursa[$i] ));
				}
			}//endfor $embed_sursa			
			
			//insert $selected_categs
			for($i=0;$i<count($selected_categs);$i++) {
				insertIntoTable("INSERT INTO mm_categs_sesizari SET sesizare_id = :sesizare_id, categ_id = :categ_id ", 
				array("sesizare_id" => $sesizareId, "categ_id" => intval($selected_categs[$i]) ));
			}//endfor $selected_categs							
			
			//send sezisare to validation
			sendSesizareToApprover($sesizareId);
										
			//succes: go to Place Signature Boxes
			redirect(add_querystring_var($_SERVER['REQUEST_URI'],'m','101'));			
			
			
			
			
		endif;								
	} else {
		//not submited
		$sendAllSubmitOK = 0;
		$data_ora = date("Y-m-d H:00");
		$selected_categs = array();
	}
	
	
	$config['currentMenuSection'] = 'adauga';
	pageHeader();
	
	
?>       

<div class="container_12">
<form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" id="sendForm" class="noEnterSubmit">        
	<div class="grid_6">

		<div class="formBox">
        	<div class="formBoxTitle"><label for="titlu">TITLU SESIZARE</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput"><input<?php if($formErr['titlu']) { print(' class="error"'); } ?> name="titlu" id="titlu" type="text" value="<?php echo(htmlspecialchars($titlu)); ?>" /></div>
        </div>

		<div class="formBox">
        	<div class="formBoxTitle"><label for="descriere">DESCRIERE</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput"><textarea<?php if($formErr['descriere']) { print(' class="error"'); } ?> name="descriere" id="descriere"><?php echo(($descriere)); ?></textarea></div>
        </div>        
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="data_ora">DATA și ORA</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput">
            	<input<?php if($formErr['data_ora']) { print(' class="error"'); } ?> class="date_adauga" name="data_ora" id="data_ora" type="text" value="<?php echo(htmlspecialchars($data_ora)); ?>" />
				<script>
					$('#data_ora').datetimepicker({ maxDate: 0  });
                </script>                
            </div>
        </div> 
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="categs">CATEGORII</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput">
            	<?php
					generateCategoriesCheckboxes($selected_categs);
				?>
            </div>
        </div>                
        
    </div>
    <div class="grid_6">
    
		<div class="formBox">
        	<div class="formBoxTitle"><label for="location_search">LOCALIZARE</label><span class="req">(obligatoriu)</span><div class="clear"></div></div>
            <div class="formBoxInput">
            	<div id="map" class="mapAdauga"></div>
                <div class="clear"></div>
            	<br /><input name="location_search" id="location_search" type="text" placeholder="Caută adresa..." value="" />
                <div id="search_location_btn" class="search_location_btn"></div>
                <input name="coord_lon" id="coord_lon" type="hidden" value="" />
                <input name="coord_lat" id="coord_lat" type="hidden" value="" />
            </div>
        </div>

		<div class="formBox">
        	<div class="formBoxTitle"><label for="link_sursa[]">LINKURI SURSA</label><div class="clear"></div></div>
            <div class="formBoxInput">
            	<input name="linkuri_sursa[]" id="link_sursa_0" type="text" class="link_sursa" value="" />
                <div id="add_link" title="Adauga inca un link catre sursa"></div>
                <div class="clear"></div>
            </div>
        </div>    
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="file_input[]">INCARCARE IMAGINE:</label><div class="clear"></div></div>
            <div class="formBoxInput"><input class="file_input<?php if($formErr['file_input']) { print(' error'); } ?>" name="file_input[]" id="file_input_0" type="file" /></div>
            <div id="add_file_input" title="Adauga inca o fotografie"></div>
            <div class="clear"></div>
        </div>   

        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="embed_sursa[]">VIDEO EMBED:</label><div class="clear"></div></div>
            <div class="formBoxInput">
            	<input name="embed_sursa[]" id="embed_sursa_0" type="text" class="embed_sursa" value="" />
                <div id="add_embed" title="Adauga cod embed video"></div>
                <div class="clear"></div>
            </div>
        </div>               
            
        
        <div class="personalInfoWrapper">
            <h2 class="personalInfo">Informatii optionale:</h2>
            <span class="personalInfoExtra">(nu vor aparea public pe site)</span>
            <div class="clear"></div>
        </div>
        
        
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="nume">NUME</label><div class="clear"></div></div>
            <div class="formBoxInput"><input name="nume" type="text" value="<?php echo(htmlspecialchars($nume)); ?>" /></div>
        </div>        
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="prenume">PRENUME</label><div class="clear"></div></div>
            <div class="formBoxInput"><input name="prenume" type="text" value="<?php echo(htmlspecialchars($prenume)); ?>" /></div>
        </div>             
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="email">E-MAIL</label><div class="clear"></div></div>
            <div class="formBoxInput"><input name="email" type="text" value="<?php echo(htmlspecialchars($email)); ?>" /></div>
        </div>        
        
		<div class="formBox">
        	<div class="formBoxTitle"><label for="telefon">TELEFON</label><div class="clear"></div></div>
            <div class="formBoxInput"><input name="telefon" type="text" value="<?php echo(htmlspecialchars($telefon)); ?>" /></div>
        </div>                                  
		
        <div class="formExtraInfo">
        	<?php echo(strip_tags(getPageText(3),'<br>')); ?>
        </div>
        
        <div class="btnAdaugaWrapper">
        	 <input type="text" name="email-title" value="" class="required-input-ffq" />
        	 <button class="btnAdauga" name="btn_adauga" id="btn_adauga">TRIMITE</button>
             <input type="hidden" name="btn_adauga_submit" value="1" />
             
             <div class="clear"></div>
        </div>
        <div class="clear"></div>
        
        
    </div>
    <div class="clear"></div>    

</form>    
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
