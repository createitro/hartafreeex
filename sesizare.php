<?php
	require_once('includes/config.php');	
	
	$sesizare_id = intval($_GET['s']);
	$_SESIZARE = getQueryInArray("SELECT * FROM sesizari WHERE validated = 1 AND deleted = 0 AND sesizare_id = :sesizare_id LIMIT 1", array('sesizare_id' => $sesizare_id));
	if(!count($_SESIZARE)) { redirect(add_querystring_var($config['siteURL'],'m','102')); }
	
	$_CATEGORII_SESIZARE = getQueryInArray("SELECT c.* FROM mm_categs_sesizari mc JOIN categories c ON mc.categ_id = c.categ_id WHERE mc.sesizare_id = :sesizare_id", array('sesizare_id' => $sesizare_id));
	
	$_LINKURI_SESIZARE = getQueryInArray("SELECT l.* FROM mm_sesizari_linkuri l WHERE l.sesizare_id = :sesizare_id AND l.link_sursa <> '' ", array('sesizare_id' => $sesizare_id));
	
	$_IMAGINI_SESIZARE = getQueryInArray("SELECT i.* FROM mm_sesizari_images i WHERE i.sesizare_id = :sesizare_id ", array('sesizare_id' => $sesizare_id));
	
	$_CODURI_EMBED = getQueryInArray("SELECT e.* FROM mm_sesizari_embeds e WHERE e.sesizare_id = :sesizare_id AND e.embed_sursa <> '' ", array('sesizare_id' => $sesizare_id));
	
	

	$config['currentMenuSection'] = 'index';
	$config['currentSubMenuSection'] = 'sesizare';
	pageHeader();
?>       
        
<div class="container_12">
	<div class="grid_7">
		<h1><?php echo('<a href="'.getSesizareURL($_SESIZARE[0]['sesizare_id']).'">'.$_SESIZARE[0]['sesizare_titlu'].'</a>'); ?></h1>
	    <div class="icon_time"></div><div class="sesizareMeta"><?php echo( getDataOraNice($_SESIZARE[0]['data_ora']) ); ?></div>
        <div class="icon_location"></div><div class="sesizareMeta"><?php echo( getLocationNice($_SESIZARE[0]['location_search'], $_SESIZARE[0]['location_reverse']) ); ?></div>
        <div class="clear"></div>
        <br />
        <h3>Categorii:</h3>
        <div class="sesizareCategsList">
        	<?php
				for($i=0;$i<count($_CATEGORII_SESIZARE);$i++) {
					print $_CATEGORII_SESIZARE[$i]['categ_name'].'';
					if($i<count($_CATEGORII_SESIZARE)-1) { print '&nbsp; | &nbsp;'; }
				}//endfor
			?>
        </div>
        
        <?php 
			//image
			if(is_file($config['filesUploadDir'].$_SESIZARE[0]['file_input_thumb'])) {
				$imgTag = '<img class="sesizareMainImg" src="'.$config['webUploadDir'].$_SESIZARE[0]['file_input_thumb'].'" width="140" border="0" />';
				$imgTag = '';
			} else {
				$imgTag = '';
			}		
		?>        
        <h3>Descriere:</h3><?php echo ($imgTag); ?>
        <div class="sesizareText"><?php echo(htmlspecialchars_decode($_SESIZARE[0]['sesizare_descriere'])); ?></div>
        
        <?php if(count($_LINKURI_SESIZARE)): ?>
        <h3>Linkuri sursă:</h3>
        <div class="sesizareText">
			<?php 
				for($i=0;$i<count($_LINKURI_SESIZARE);$i++) {
					$linkURL = $_LINKURI_SESIZARE[$i]['link_sursa'];
					print '<a class="linkSursa" href="'.$linkURL.'" target="_blank">'.$linkURL.'</a><br />';
				}
			?>
        </div>        
        <?php endif; ?>
        
        <?php if(count($_IMAGINI_SESIZARE)): ?>
        <div class="clear"></div>
        <h3>Imagini:</h3>
        <div class="sesizareText">
			<?php 
				for($i=0;$i<count($_IMAGINI_SESIZARE);$i++) {
					$linkURL = $_LINKURI_SESIZARE[$i]['link_sursa'];
					echo '<img class="alteImaginiSesizareImg" src="'.$config['webUploadDir'].$_IMAGINI_SESIZARE[$i]['file_input_thumb'].'" width="140" border="0" />'."\n";
				}
			?>
            <div class="clear"></div>
        </div>        
        <div class="clear"></div>
        <?php endif; ?>  
        

        <?php if(count($_CODURI_EMBED)): ?>
        <h3>Video:</h3>
        <div class="sesizareText">
			<?php 
				for($i=0;$i<count($_CODURI_EMBED);$i++) {
					$codEmbed = '<div class="videoContainer">'.$_CODURI_EMBED[$i]['embed_sursa'].'</div>';
					print $codEmbed.'<div class="clear"></div>';
				}
			?>
        </div>        
        <?php endif; ?>              
        
        
        <br />
        <h3>Comentează:</h3>
        
        <div class="commentsTabs">
        	<div id="tab_sesizariCommentsFB" class="commentsTab sesizariCommentsFB_ON"></div>
            <div id="tab_sesizariCommentsDisqus" class="commentsTab sesizariCommentsDisqus_OFF"></div>
            <div id="tab_sesizariCommentsIntense" class="commentsTab sesizariCommentsIntense_OFF"></div>
            <div class="clear"></div>
        </div>

        <div id="sesizariCommentsFB" class="commentsDiv">
	        <div class="fb-comments" data-href="<?php echo('http://'.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']); ?>" data-numposts="5" data-colorscheme="light"></div>
        </div>
        
        <div id="sesizariCommentsDisqus" class="commentsDiv"> 
            <div id="disqus_thread"></div>
            <script type="text/javascript">
                var disqus_shortname = 'hartainteractiva';
                
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
        </div>
        
        
        
        <div id="sesizariCommentsIntense" class="commentsDiv">
			<script>
            var idcomments_acct = 'a143dcfb997c05ba49cd52ed54edf3eb';
            var idcomments_post_id;
            var idcomments_post_url;
            </script>
            <span id="IDCommentsPostTitle" style="display:none"></span>
            <script type='text/javascript' src='http://www.intensedebate.com/js/genericCommentWrapperV2.js'></script>        
        </div>

        <br /><br /><br /><br /><br /><br />
        <div class="clear"></div>
        
    </div>
    <div class="grid_5">
		<div id="map" class="sesizareMap"></div>
        <div class="shareThisWrapper">
        	<h3>Distribuie:</h3>
            <div class="shareThisWrapperIcons"><?php shareThisOnPage(); ?></div>
        </div>
        
    </div>
</div>

    <script type="text/javascript">
        var map, layer, vectorLayer, featureMarker;
		<?php 
			$markersJSString = '';
			$markersJSString .= "{'ID':'".intval($_SESIZARE[0]['sesizare_id'])."', 'NAME':'".jsspecialchars($_SESIZARE[0]['sesizare_titlu'])."', 'LATITUDE':".$_SESIZARE[0]['coord_lat'].", 'LONGITUDE':".$_SESIZARE[0]['coord_lon']."}";
		?>
		var markers = [<?php echo($markersJSString); ?>];		
		
        function initIndex() {}
		
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
					{description: decode_utf8(markers[i].NAME) } ,
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
