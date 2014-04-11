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
!function($){
	function __isset(obj){
		if(obj){
			for(var k in obj){
				return false;
			}
		}
		return true;
	}
	function __paramsToObject(part){
		var ret = {};
		var rows = part.split("&");
		$.each(rows, function(){
			var elems = ("" + this).split("=");
			if(elems[0]){
				ret[elems[0]] = elems[1] || null;
			}
		});
		return ret;
	}
	function __objectToParams(params){
		var as = [];
		$.each(params, function(key, val){
		   as.push(val ? [key,val].join("=") : key);
		});
		return as.join("&");
	}
	function __parseURL(url){
		var elems = url.split(/[\?#]/);
		var hasParam = url.match(/\?/);
		return {
			path: elems[0],
			params: __paramsToObject(hasParam ? elems[1] || "" : "") ,
			fragments: __paramsToObject(hasParam ? elems[2] || "" : elems[1] || "")
		};
	}
	function __toUrl(url){
		return [url.path,
			(__isset(url.params) ? "" : "?" + __objectToParams(url.params)),
			(__isset(url.fragments) ? "" : "#" + __objectToParams(url.fragments))
		].join("");
	}

	$.extend({
		parse_url:function(sUrl){
			return __parseURL(sUrl);
		},
		serialize_url: function(oUrl){
			return __toUrl(oUrl);
		}
	});
}(window.jQuery);
