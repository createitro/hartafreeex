<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
	require_once('includes/config.php');	



	//Process Send Document Form
	$sendCautaSubmitOK = 1;	
	

	if(strlen($_REQUEST['btn_cauta_submit'])) {
		
		//Sesizare Categorii
		$selected_categs = array();
		foreach ($_REQUEST as $key => $value) {
		  //just checkboxes
		  if(begins_with($key,'cb')) { $selected_categs[] = $value;  }
	    }
		$selected_categs_string = implode ( ',' , $selected_categs);

		//Sesizare Data si Ora Start
		$date_start = filter_var($_REQUEST['date_start'], FILTER_SANITIZE_STRING);
		if(!strlen($date_start)) { $formErr['date_start'] = 'Va rugam completati data start!'; $sendCautaSubmitOK = 0;	}	
		
		//Sesizare Data si Ora Sfarsit
		$date_end = filter_var($_REQUEST['date_end'], FILTER_SANITIZE_STRING);
		if(!strlen($date_end)) { $formErr['date_end'] = 'Va rugam completati data sfarsit!'; $sendCautaSubmitOK = 0;	}				
		
	
		//print_nice($selected_categs); exit();
		
		//Sesizare Localizare Coordonate
		$coord_lon = floatval($_REQUEST['coord_lon']);
		$coord_lat = floatval($_REQUEST['coord_lat']);
		
		$q = filter_var($_REQUEST['q'], FILTER_SANITIZE_STRING);
		
		$location_search = filter_var($_REQUEST['location_search'], FILTER_SANITIZE_STRING);
										
	} else {
		//default values
		$sendCautaSubmitOK = 1;
		$selected_categs = array();
		$selected_categs_string = implode ( ',' , $selected_categs);
		$date_start = date("Y-m-d", $config['time'] - abs(2*12*30*24*60*60));
		$date_end = date("Y-m-d");
		$q = '';

	}
	
	//Do the Search
	$selectStr = "SELECT DISTINCT(s.sesizare_id), s.* FROM `mm_categs_sesizari` mc
	LEFT JOIN `sesizari` s ON mc.sesizare_id = s.sesizare_id
	WHERE s.deleted = 0 AND s.validated = 1 ";
	if(count($selected_categs)) {
		$selectStr .= " AND mc.categ_id IN (".$selected_categs_string.")";
	}	
	if(strlen($date_start)) { $selectStr .= " AND data_ora >= '".$date_start." 00:00:00' "; }
	if(strlen($date_end)) { $selectStr .= " AND data_ora <= '".$date_end." 23:59:59'"; }
	
	if(strlen($q)) {
		$selectStr .= " AND ( sesizare_titlu LIKE :sesizare_titlu OR sesizare_descriere LIKE :sesizare_descriere )";
		$qArray = array('sesizare_titlu' => '%'.$q.'%', 'sesizare_descriere' => '%'.$q.'%');
	} else {
		$qArray = array();
	}
	
	//echo $selectStr; print_nice($selected_categs);
	
	$selectStr .= " ORDER BY data_ora DESC";
	
	$_SESIZARI = getQueryInArray($selectStr, $qArray );
	
	pageHeader();

?>       
<form action="<?php echo($_SERVER['REQUEST_URI']); ?>" method="get" enctype="multipart/form-data" id="sendForm" class="noEnterSubmit">           
<div class="container_12">
	<div class="grid_3">
    	<div class="box">
            <div class="boxTitle">Categorii</div>
            <div class="categsCheckBoxes">                
               	<?php
					generateCategoriesCheckboxes($selected_categs);
				?>            
                
            </div>
        </div>
        <div class="clear"></div>
        
    	<div class="box">
            <div class="boxTitle">Perioada</div>
            <div class="dateControlsInterval">
            	<input class="date<?php if($formErr['date_start']) { print(' error'); } ?>" type="text" name="date_start" id="date_start" value="<?php echo(htmlspecialchars($date_start)); ?>" maxlength="20" readonly />
                <script> $.datepicker.setDefaults($.datepicker.regional['ro']); </script>
				<script> $(function() { $( "#date_start" ).datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true }); }); </script>                
                <div class="arrowRight"></div>
            	<input class="date<?php if($formErr['doc_name']) { print(' error'); } ?>" type="text" name="date_end" id="date_end" value="<?php echo(htmlspecialchars($date_end)); ?>" maxlength="20" readonly />
				<script> $(function() { $( "#date_end" ).datepicker({ dateFormat: 'yy-mm-dd', changeMonth: true, changeYear: true }); }); </script>                                
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>

    	<div class="box">
            <div class="boxTitle">Cuvânt cheie:</div>
            <div class="boxControlsLocation">
            	<input type="text" name="q" id="q" value="<?php echo(htmlspecialchars($q)); ?>" />
            </div>
        </div>        
        <div class="clear"></div>
        
        <!--
    	<div class="box">
            <div class="boxTitle">Localizare</div>
            <div class="boxControlsLocation">
            	<input type="text" name="location" id="location" value="" />
            </div>
        </div>        
        <div class="clear"></div>
        
        -->
        
        
        <div class="box">
        	<div class="stergeFiltre"><a class="stergeFiltre" href="./">Șterge filtre</a></div>
            <button class="btnCauta" name="btn_cauta" id="btn_cauta">CAUTĂ</button>
            <input type="hidden" name="btn_cauta_submit" value="1" />
        </div>
        <br /><br /><br />
        <div class="clear"></div>
        
        
    </div>
    <div class="grid_9">
    	<div id="map" class="mapIndex">
        </div>
        
        <div class="reportsList">
        	<?php
			//PRINT SESIZARI
			$limitInList = 30;
			for($i=0;$i<count($_SESIZARI);$i++) {
				if(($i%$limitInList) == 0) {
					if($i != 0) { $extraGroupClass = ' sesizare_group_hidden'; } else { $extraGroupClass = ''; }
					print '<div class="group'.$extraGroupClass.'">';
				}
				
				//PRINT SESIZARE
				//print '('.($i%$limitInList).')';
				printSesizareInList($_SESIZARI[$i]);
				
				if(((($i+1)%$limitInList) == 0) || ($i == (count($_SESIZARI) - 1))) {
					print '</div>';
				}				
			} //endfor
			if(count($_SESIZARI) > $limitInList) {
				print '<div id="sesizari_more">vezi mai multe rezultate &blacktriangledown;</div>';
				print '<div class="clear"></div>';
			}
			?>            
            
        </div>
    </div>
</div>
</form>

    <script type="text/javascript" language="javascript">
        var map, layer, vectorLayer, featureMarker;




		<?php 
		$markersJSString = '';
		for($i=0;$i<count($_SESIZARI);$i++) {
			$markersJSString .= "{'ID':'".intval($_SESIZARI[$i]['sesizare_id'])."', 'NAME':'".jsspecialchars($_SESIZARI[$i]['sesizare_titlu'])."', 'LATITUDE':".$_SESIZARI[$i]['coord_lat'].", 'LONGITUDE':".$_SESIZARI[$i]['coord_lon']."}";
			if($i < count($_SESIZARI) - 1) { $markersJSString .= ", "; }
		}	
		?>
		var markers = [<?php echo($markersJSString); ?>];	
		
		//console.log(markers);
		console.log("==========SORTING================");
		bubbleSortMarkersLon(markers);
		//console.log(markers);
		
		console.log("==========MAP=MARKERS============");
		var MAP_MARKERS = [];
		var k = 0;
		MAP_MARKERS[k] = [];
		MAP_MARKERS[k]['DESC'] = '';
		for (var i=0; i<markers.length; i++) {
			MAP_MARKERS[k]['DESC'] += '&bull; <a class="popupAnchor" href="sesizare.php?s='+markers[i].ID+'">' + decode_utf8(markers[i].NAME) + '</a><div class="popupClear"><br /></div>';
			MAP_MARKERS[k]['LONGITUDE'] = markers[i].LONGITUDE;
			MAP_MARKERS[k]['LATITUDE'] = markers[i].LATITUDE;
			if((i < markers.length - 1) && (markers[i].LONGITUDE != markers[i+1].LONGITUDE)) {
				k++;
				MAP_MARKERS[k] = [];
				MAP_MARKERS[k]['DESC'] = '';
			}
		}
		console.log(MAP_MARKERS);
			
		
        function initIndex() {
            //OpenLayers.ProxyHost = "openlayers/examples/proxy.cgi?url=";
            map = new OpenLayers.Map('map', {
                controls: [new OpenLayers.Control.PanZoom(), new OpenLayers.Control.Navigation() ]
            });
			
            layer = new OpenLayers.Layer.OSM("OpenStreetMap", null, { transitionEffect: 'resize' });
			/*
			layer = new OpenLayers.Layer.OSM("Stamen toner", 
											   ["http://tile.stamen.com/toner/${z}/${x}/${y}.png"], 
												{attribution: "&copy; <a href='http://www.openstreetmap.org/'>OpenStreetMap</a> and contributors, under an <a href='http://www.openstreetmap.org/copyright' title='ODbL'>open license</a>. Toner style by <a href='http://stamen.com'>Stamen Design</a>",
												"tileOptions": { "crossOriginKeyword": null }});
			*/									
			
            map.addLayers([layer]);
			
		    /*
            map = new OpenLayers.Map('map', {
                controls: [new OpenLayers.Control.PanZoom(), new OpenLayers.Control.Navigation() ]
            });
			
            layer = new OpenLayers.Layer.OSM("OpenStreetMap", null, { transitionEffect: 'resize' });
            map.addLayers([layer]);
						
			map.addLayer(new OpenLayers.Layer.OSM("Stamen toner", 
											   ["http://tile.stamen.com/toner/${z}/${x}/${y}.png"], 
												{attribution: "&copy; <a href='http://www.openstreetmap.org/'>OpenStreetMap</a> and contributors, under an <a href='http://www.openstreetmap.org/copyright' title='ODbL'>open license</a>. Toner style by <a href='http://stamen.com'>Stamen Design</a>",
												"tileOptions": { "crossOriginKeyword": null }}));
			*/
			
			//projection (for transform)
			var epsg4326 =  new OpenLayers.Projection("EPSG:4326"); //WGS 1984 projection
			var projectTo = map.getProjectionObject(); //The map projection (Spherical Mercator)			
			
			//vector
			vectorLayer = new OpenLayers.Layer.Vector("Vector Layer");
			    
			//Loop through the markers array
			for (var i=0; i<MAP_MARKERS.length; i++) {
				//remove alst <br/>
				if (MAP_MARKERS[i].DESC.lastIndexOf('<br/>') == MAP_MARKERS[i].DESC.length - 5) {
				  MAP_MARKERS[i].DESC = MAP_MARKERS[i].DESC.slice(0, -5);
				}				
				var feature = new OpenLayers.Feature.Vector(
						new OpenLayers.Geometry.Point( MAP_MARKERS[i].LONGITUDE, MAP_MARKERS[i].LATITUDE ).transform(epsg4326, projectTo),
						{description: (MAP_MARKERS[i].DESC) } ,
						{externalGraphic: './openlayers/img/marker.png', graphicHeight: 25, graphicWidth: 21, graphicXOffset:-18, graphicYOffset:-0  }
					);             
				vectorLayer.addFeatures(feature);
			} 		
			/*
			//Loop through the markers array
			for (var i=0; i<markers.length; i++) {
				var feature = new OpenLayers.Feature.Vector(
						new OpenLayers.Geometry.Point( markers[i].LONGITUDE, markers[i].LATITUDE ).transform(epsg4326, projectTo),
						{description: '<a class="popupAnchor" href="sesizare.php?s='+markers[i].ID+'">' + decode_utf8(markers[i].NAME) + '</a>' } ,
						{externalGraphic: './openlayers/img/marker.png', graphicHeight: 25, graphicWidth: 21, graphicXOffset:-18, graphicYOffset:-0  }
					);             
				vectorLayer.addFeatures(feature);
			} 
			*/					
			
				
			//add vector layer
			map.addLayer(vectorLayer);
			

			//Add a selector control to the vectorLayer with popup functions
			var controls = {
			  selector: new OpenLayers.Control.SelectFeature(vectorLayer, { onSelect: createPopup, onUnselect: destroyPopup })
			};
		
			function createPopup(feature) {
			  feature.popup = new OpenLayers.Popup.FramedCloud("pop",
				  feature.geometry.getBounds().getCenterLonLat(),
				  null,
				  '<div class="markerContent">'+feature.attributes.description+'</div>',
				  null,
				  true,
				  function() { controls['selector'].unselectAll(); }
			  );
			  //feature.popup.closeOnMove = true;
			  map.addPopup(feature.popup);
			}
		
			function destroyPopup(feature) {
			  feature.popup.destroy();
			  feature.popup = null;
			}
			
			map.addControl(controls['selector']);
			controls['selector'].activate();


			
			
			//center map default [Romania]
			var positionRomaniaDefault       = new OpenLayers.LonLat(25.0094303, 45.9442858);
			map.setCenter(positionRomaniaDefault.transform(epsg4326, projectTo), 7);
			
		
        }
		
		
        function submitSearchLocation(queryString) {
            //var queryString = document.forms[0].query.value;
			$("#search_location_btn").removeClass("search_location_btn").addClass("loader_21");
            OpenLayers.Request.POST({
                url: "http://www.openrouteservice.org/php/OpenLSLUS_Geocode.php",
                scope: this,
                failure: this.requestFailure,
                success: this.requestSuccess,
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                data: "FreeFormAdress=" + encodeURIComponent(queryString) + "&MaxResponse=1"
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
            alert("Eroare de comunicare cu serviciul OpenLS. Va rugam incercati din nou.");
        }
    </script> 
<?php

	pageFooter();

?>
