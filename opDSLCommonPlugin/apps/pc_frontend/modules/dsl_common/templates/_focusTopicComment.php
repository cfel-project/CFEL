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
<script type="text/javascript">
$(document).ready(function(){
	function __isset(obj){
		if(obj){
			for(var k in obj){
				return false;
			}
		}
		return true;
	}
	function paramsToObject(part){
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
	function objectToParams(params){
		var as = [];
		$.each(params, function(key, val){
		   as.push(val ? [key,val].join("=") : key);
		});
		return as.join("&");
	}
	function parseURL(url){
		var elems = url.split(/[\?#]/);
		var hasParam = url.match(/\?/);
		return {
			path: elems[0],
			params: paramsToObject(hasParam ? elems[1] || "" : "") ,
			fragments: paramsToObject(hasParam ? elems[2] || "" : elems[1] || "")
		};
	}
	function toUrl(url){
		return [url.path,
			(__isset(url.params) ? "" : "?" + objectToParams(url.params)),
			(__isset(url.fragments) ? "" : "#" + objectToParams(url.fragments))
		].join("");
	}

	var __update = function(){
		var url = parseURL(window.location.href);
		if(url.fragments && url.fragments.comment){
			var node = $("div.commentList>div.parts>dl [cmt_number='" + url.fragments.comment + "'])");
			if(node){
				$("body").animate({
					scrollTop: $(node).offset().top
				}, 400);
				$(node).focus();
			}
		}
	}
	var num_comments = $("div.commentList>div.parts>dl").length;

	var __fixup = function(){
		$(".commentList .parts .partsHeading h3").after("<span>(" + num_comments + "件)</span>");
		$(".commentList .parts div.pagerRelative p.number").hide();
	};

	if(num_comments){
		$("div.commentList>div.parts>dl").each(function(){$(this).attr("id", "cmt_number_" + $(this).find("dd>.title>.heading>strong").text());})
		if(num_comments > 2){
			$('<ul class="moreInfo"><li style="float:right;"><a href="#cmt_number_' + num_comments + '">最後のコメントへ移動</a></li></ul>')
			 .appendTo($(".commentList .parts div.pagerRelative:first"));
			$('<ul class="moreInfo"><li style="float:right;"><a href="#cmt_number_1">最初のコメントへ移動</a></li></ul>')
			 .appendTo($(".commentList .parts div.pagerRelative:last"));
		}
		__fixup();
	}
	__update();

});
</script>
