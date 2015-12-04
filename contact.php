<?php
	require_once('includes/config.php');	



	$config['currentMenuSection'] = 'contact';
	pageHeader();
	
	$embed_1 = '<a href="http://freeex.activewatch.ro/" target="_blank"><img src="http://freeex.activewatch.ro/banners/freeex_728x90.jpg" /></a>';
	$embed_2 = '<a href="http://freeex.activewatch.ro/" target="_blank"><img src="http://freeex.activewatch.ro/banners/freeex_300x250.jpg" /></a>';
	$embed_3 = '<a href="http://freeex.activewatch.ro/" target="_blank"><img src="http://freeex.activewatch.ro/banners/freeex_160x600.jpg" /></a>';
	
?>       
        
<div class="container_12">
	<div class="grid_7">
		<h1>ActiveWatch - FreeEx </h1><br />
		<?php echo(getPageText(5)); ?>
	    <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>         
        <h2>Embed Harta FreeEx:</h2>     
        <p>&nbsp;</p> 
        <input type="text" class="select_embed" value="<?php echo(htmlspecialchars($embed_1)); ?>" /> <a class="select_embed_link" href="http://freeex.activewatch.ro/banners/freeex_728x90.jpg" target="_blank">728x90 (deschide)</a><br /><br />
        <input type="text" class="select_embed" value="<?php echo(htmlspecialchars($embed_2)); ?>" /> <a class="select_embed_link" href="http://freeex.activewatch.ro/banners/freeex_300x250.jpg" target="_blank">300x250 (deschide)</a><br /><br />
        <input type="text" class="select_embed" value="<?php echo(htmlspecialchars($embed_3)); ?>" /> <a class="select_embed_link" href="http://freeex.activewatch.ro/banners/freeex_160x600.jpg" target="_blank">160x600 (deschide)</a><br /><br />          
        
        
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>          
        
        
    </div>
    <div class="grid_5">
    	<div id="map" class="contactMap"></div>
    </div>
</div>




    <script type="text/javascript">
        var map, layer, vectorLayer, featureMarker;
		<?php 
			$markersJSString = '';
			$markersJSString .= "{'ID':'".intval(1)."', 'NAME':'".jsspecialchars('ActiveWatch - FreeEx ')."', 'LATITUDE':".'44.438763'.", 'LONGITUDE':".'26.076325'."}";
		?>
		var markers = [<?php echo($markersJSString); ?>];		
		
        function initContact() {}
		
		//OpenLayers.ProxyHost = "openlayers/examples/proxy.cgi?url=";
		map = new OpenLayers.Map('map', {
			controls: [new OpenLayers.Control.PanZoom(), new OpenLayers.Control.Navigation() ]
		});
		
		layer = new OpenLayers.Layer.OSM("OpenStreetMap", null, { transitionEffect: 'resize' });
		map.addLayers([layer]);
		
		/*
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
		for (var i=0; i<markers.length; i++) {
			var feature = new OpenLayers.Feature.Vector(
					new OpenLayers.Geometry.Point( markers[i].LONGITUDE, markers[i].LATITUDE ).transform(epsg4326, projectTo),
					{description: markers[i].NAME} ,
					{externalGraphic: './openlayers/img/marker.png', graphicHeight: 25, graphicWidth: 21, graphicXOffset:-18, graphicYOffset:-0  }
				);             
			vectorLayer.addFeatures(feature);
		} 			
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
		var positionRomaniaDefault       = new OpenLayers.LonLat(markers[0].LONGITUDE, markers[0].LATITUDE);
		map.setCenter(positionRomaniaDefault.transform(epsg4326, projectTo), 14);
		
	
        
		

    </script> 
    
<?php

	pageFooter();

?>
