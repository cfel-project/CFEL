<?php
/*******************************************************************************
 * Copyright (c) 2011, 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
?>
<?php use_helper('opMap') ?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=visualization&language=ja"></script>
<div id="homeRecentList_<?php echo $gadget->id ?>" class="dparts"><div class="parts">
	<div class='partsHeading'>
		<h3><?php echo $u_name ?> さんの周辺情報 (<?php echo $u_homeCaption ?>)</h3>
	</div>
	<div class='block'>
		<div class='gmap' style='width:100%;height:350px;border-radius:6px;' id='sc_map_frame_<?php echo $gadget->id ?>'>
		</div>
	</div>
</div></div>
<script type="text/javascript">
(function(){
var pne_current_user = {
	name: "<?php echo $u_name ?>",
	home: "<?php echo $u_homeLocation ?>"
};
var trgId = "sc_map_frame_"+ "<?php echo $gadget->id ?>";

var _bounds = new google.maps.LatLngBounds();

var _fitBounds = function(map, bounds){
	if(!map.__fitBounds_inflight){
		map.__fitBounds_inflight = true;
		setTimeout(function(){
			map.__fitBounds_inflight = false;
			map.fitBounds(bounds);
			if(map.getZoom() > 13){
				map.setZoom(13);
			}
		}, 1000);
	}
}

var __infoWindow = new google.maps.InfoWindow;
var __showInfoWindow = function(map, marker, content){
	__infoWindow.setContent(content);
	__infoWindow.open(map, marker);
}
var __hideInfoWindow = function(){
	__infoWindow.close();
}
var addMarker = function(itm, map){
	var item = itm;
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		"address": item.author.location,
		"region": "jp",
	}, function(results, status){
		var latLng = (status == google.maps.GeocoderStatus.OK) ? results[0].geometry.location : hongou;
		var content = ["<div style='font-size: 123%;font-weight: bold;'>",
			  		(item.subject || "") + (item.body || ""),
			  		"(" + item.replies + ")",
			  		"</div>",
			  		"<div>",
			  		"" + (new Date(item.created)),
			  		"  by ",
			  		item.author.name,
			  		"</div>"].join("");
		var marker = new google.maps.Marker({
    		map:map,
    		draggable:true,
    		animation: google.maps.Animation.DROP,
    		position: latLng,
    		title: (item.subject || "") + (item.body || "")});
		google.maps.event.addListener(marker, "click", function(e){
			__showInfoWindow(map, marker, content);
		});
		_bounds.extend(marker.getPosition());
		_fitBounds(map, _bounds);
   	});
}
var hongou = new google.maps.LatLng(35.713427,139.762308);

var start = function(){
	var geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		"address": pne_current_user.home,
		"region": "jp"
	}, function(results, status){
		initMap(document.getElementById(trgId)
			, (status == google.maps.GeocoderStatus.OK) ? results[0].geometry.location : hongou);
	});
}
var initMap = function (trg, center){
	var map = new google.maps.Map(trg,{
		zoom:13,
		center: center,
		mapTypeId:google.maps.MapTypeId.ROADMAP
	});
	$.ajax({
		type: "GET",
		url: openpne.apiBase + "dsls/activities",
		dataType:"json",
		data:{
			size: 8,
			apiKey: openpne.apiKey
		},
		success: function(result){
			if(result.num){
				var activities = result.items;
	for (var i = 0;i < activities.length; i++){
		(function(){
			var actv = activities[i];
			setTimeout(function(){addMarker(actv,map);}, 500 * i);
		})();
//		addMarker(activities[i], map);
	}
			}
		},
		error: function(xhr, status, e){
		}
	});

	google.maps.event.addListener(map, "click", function(e){
		__hideInfoWindow();
	});
/*    google.maps.event.addListener(map, 'click', function(e){
    	var marker = new google.maps.Marker({
    		map:map,
    		draggable:false,
    		animation: google.maps.Animation.DROP,
    		position: e.latLng,
    		title: "Probably your name with comment comes here?"
    	})
    });*/
};

start();

})();
</script>
