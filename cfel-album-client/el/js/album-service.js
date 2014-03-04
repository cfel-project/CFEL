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

var cfelService = {
	serviceRoot : "/el_api/cfel-web-service/",

	getPhotoList : function(findOptions, callback) {
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
