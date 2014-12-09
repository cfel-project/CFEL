/*******************************************************************************
 * Copyright (c) 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
(function($){
$.fn.el_control_append_to = function(options){
	var trg = target.el_control_append ? target : target.get(0);
	if(trg.el_control_append){
		trg.el_control_append(this, options);
	}
	return this;
};

$.fn.el_map_view = function(options){
	var service_root = options.service_root || cfelService.serviceRoot.slice(0, -1);
	var self = this;
	var opt = $.extend({
		zoom: options.zoom || 18,
		center: options.home ? new google.maps.LatLng(options.home.lat(), options.home.lng()) : options.center,
		mapTypeId: options.mapTypeId || google.maps.MapTypeId.HYBRID
	}, options);
	var map = new google.maps.Map($(this).get(0), opt);
	self.el_map_obj = map;
	var home = opt.center;
	if(home){
		map.panTo(home);
	}else{
		setTimeout(function(){
			home = map.getCenter();
		},10);
	}

	self.el_add_control = function(dir, elm){
		map.controls[dir].push(elm.get ? elm.get(0) : elm);
	};

	self.el_map_home = function(){
		map.panTo(home);
		setTimeout(function(){
			map.setOptions(opt);
		}, 300);
	};
	self.el_control_append = function(elem, options){
		map.controls[options.position || google.maps.ControlPosition.RIGHT_TOP].push($(elem).get(0));
		return this;
	};

var _icon_size = new google.maps.Size(130,100);
var _icon_offset = new google.maps.Point(0,0);
var _icon_anchor = new google.maps.Point(65,100);
var _icon_scaleSize = new google.maps.Size(130,100);
var _markers = [];
var _marker_handlers = [];
var _marker_item_map = {};

function __get_item_ll(item){
	var lat = item.annotation_location ? item.annotation_location[item.annotation_location.length-1].lat : item.exif_location ? item.exif_location[1] : null;
	var lng = item.annotation_location ? item.annotation_location[item.annotation_location.length-1].lon : item.exif_location ? item.exif_location[0] : null;
	if(lat && lng){
		return new google.maps.LatLng(lat, lng);
	}else{
		return null;
	}
}

	self.el_get_item_ll = __get_item_ll;

	self.el_marker_append = function(item, options){
		var title = (item.annotation_title ? item.annotation_title[item.annotation_title.length-1].title : item.title) || "このリンクをタッチすると大きな画像が見えます";
		var url = (item.portal_image_url || "").replace(/\/photo/, "photo");
		var m_url = service_root + item.marker_image_url;
		var image = new google.maps.MarkerImage(m_url, _icon_size, _icon_offset, _icon_anchor, _icon_scaleSize);
		var i_html = $("<div/>", {
			style:"overflow:hidden;"
		}).append($("<a/>",{
			"href": url,
			"target": "_blank",
			"style": "display:block;font-size:large;"
		}).html(title)).get(0).outerHTML;
		var pos = __get_item_ll(item) || map.getCenter();
		if(!options || !options.no_pan){
			map.panTo(pos);
		}
		var mrkr = __create_marker(i_html, pos, image);
		_markers.push(mrkr);
		_marker_item_map[mrkr] = item;
		return this;
	};
	self.el_marker_clear = function(){
		_marker_handlers.forEach(function(elm, idx){
			for(var k in elm){
				google.maps.event.removeListener(elm[k]);
			}
		});
		_marker_handlers = [];
		_markers.forEach(function(elm, idx){
			elm.setMap(null);
		});
		_markers = [];
	};

function __create_marker(i_html, latlng, image){
	var i_window = new google.maps.InfoWindow();
	var marker = new google.maps.Marker({
		position: latlng,
		map: map,
		icon: image,
		animation: google.maps.Animation.DROP,
		draggable: true
	});
	var drag_start = null;
	var from = null;
	_marker_handlers.push({
		click: google.maps.event.addListener(marker, "click", function(ev){
			i_window.setContent(i_html);
			setTimeout(function(){
				i_window.open(map, marker);
			},200);
		}),
		dragstart: google.maps.event.addListener(marker, "dragstart", function(ev){
			drag_start = new Date();
			from = marker.getPosition();
		}),
		dragend: google.maps.event.addListener(marker, "dragend", function(ev){
			var drag_end = new Date();
			var to = marker.getPosition();
			self.trigger("marker_moved", [marker, _marker_item_map[marker], {from: from, to: to}, {start: drag_start, end: drag_end}]);
		})
	});
	return marker;
}
	return self;

};
})(jQuery);
