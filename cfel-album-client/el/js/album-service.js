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

/*
 * Service API
 */
(function(){
var __req_frg = null;
function __get_req_frg(key){
	if(!__req_frg){
		__req_frg = {};
		((document.location.hash || "").replace(/^#/,"") || "").split("&").forEach(function(elem){
			var vals = elem.split("=");
			__req_frg[vals[0]] = vals[1] || null;
			
		});
	}
	return __req_frg[key];
}

window["cfelService"] = {
	serviceRoot : "/el_api/cfel-web-service/",

	getRequestFragment: __get_req_frg,

	getPhotoList : function(findOptions, callback) {
		findOptions.query = findOptions.query || {
			"removed" : {
				"$ne" : true
			}
		};
		var options = {
			type : "GET",
			headers : {
				"X-Eld-Land-APIKey" : this.apiKey
			},
			url : this.serviceRoot + "photo",
			dataType : "json",
			success : function(data) {
				if (callback) {
					callback({
						"status" : "success",
						"data" : data
					});
				}
			},
			error : function() {
				if (callback) {
					callback({
						"status" : "error"
					});
				}
			}
		};
		if (findOptions) {
			options.data = this.toJSONParameter(findOptions, {});
		}
		$.ajax(options);
	},

	updatePhoto : function(id, updateOptions, callback) {
		var options = {
			type : "POST",
			headers : {
				"X-Eld-Land-APIKey" : this.apiKey
			},
			url : this.serviceRoot + "photo/" + id,
			dataType : "json",
			success : function(data) {
				if (callback) {
					callback({
						"status" : "success",
						"data" : data
					});
				}
			},
			error : function() {
				if (callback) {
					callback({
						"status" : "error"
					});
				}
			}
		};
		if (updateOptions) {
			options.data = this.toJSONParameter(updateOptions, {
				"action" : "update"
			});
		}
		$.ajax(options);
	},

	postPhoto : function(photo, callback) {
		var options = {
			type : "POST",
			headers : {
				"X-Eld-Land-APIKey" : this.apiKey
			},
			url : this.serviceRoot + "photo",
			data : {
				"data" : JSON.stringify(photo)
			},
			dataType : "json",
			success : function(data) {
				if (callback) {
					callback({
						"status" : "success",
						"data" : data
					});
				}
			},
			error : function() {
				if (callback) {
					callback({
						"status" : "error"
					});
				}
			}
		};
		$.ajax(options);
	},

	toJSONParameter : function(params, data) {
		for ( var key in params) {
			var obj = params[key];
			data[key] = (typeof obj) == 'object' ? JSON.stringify(obj) : obj;
		}
		return data;
	},

	getID : function(photo) {
		if (photo._id) {
			return photo._id.$oid || photo._id;
		}
	}
};

window["get_current_timestamp"] = function() {
    var weeks = new Array('日', '月', '火', '水', '木', '金', '土');
    var d = new Date();
 
    var month  = d.getMonth() + 1;
    var day    = d.getDate();
    var week   = weeks[ d.getDay() ];
    var hour   = d.getHours();
    var minute = d.getMinutes();
    var second = d.getSeconds();
 
    if (month < 10) {month = "0" + month;}
    if (day < 10) {day = "0" + day;}
    if (hour < 10) {hour = "0" + hour;}
    if (minute < 10) {minute = "0" + minute;}
    if (second < 10) {second = "0" + second;}
 
    return d.getFullYear()  + "-" + month + "-" + day + "T" + hour + ":" + minute + ":" + second + ".000Z";
};

})();

