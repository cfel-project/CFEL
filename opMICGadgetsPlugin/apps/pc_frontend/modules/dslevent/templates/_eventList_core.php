<?php
/*******************************************************************************
 * Copyright (c) 2011, 2013 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
?>
<?php include("_eventList_tmpl.php")?>
<script type="text/javascript">
$(document).ready(function(){
	var __dates = <?php echo (empty($days) ? "1" : $days) ?>;
	var __dtmls = 86400000

	var __id = "eventlist_<?php echo $gadget->id ?>";
	var __base_url = "<?php echo url_for('communityEvent/')?>";

	function __getByClass(cls){
		return $("#" + __id + " ." + cls);
	}
	var params = {
		target: "<?php echo $targetKey ?>",
		target_id: "<?php echo $targetId ?>",
		apiKey: openpne.apiKey
	}

	var __today = moment(new Date()).sod().toDate().getTime();

	function __render(data, start){
		var items = [];
		var cur = start;
		var end = start + __dtmls * __dates;
		while(cur < end){
			var cm = moment(new Date(cur));
			items.push({
				epoc: 1 * cur,
				datestr: cm.format("M/D(dd)"),
				optcls: [cur == __today ? "today" : (cur > __today ? "future" : "past"),
						"dow_" + cm.day()].join(" "),
				events:[]
			});
			cur += __dtmls;
		}
		var c_width = (__getByClass("event_container_row").width()
					 - __getByClass("event_prev").outerWidth(true)
					 - __getByClass("event_next").outerWidth(true)) / items.length - 1;
		$.each(data, function(i, val){
			var dt = moment(val.open_date, 'YYYY-MM-DD HH:mm:ss').toDate().getTime();
			if(start <= dt && end > dt){
				var idx = (dt - start) / 86400000;
				items[idx].events.push(
					$.extend({},{ev_url:__base_url + val.id + "#" + val.id}, val)
				);
			}
		});
		__getByClass("event_container")
			.empty().html($("#el_date_cell_templ")
				.tmpl(items, {cellWidth:function(){
						return c_width;
					}
				})
			);
		__getByClass("event_container [link_href]").click(function(){
			window.location.href = $(this).attr("link_href");
		});
	}
	function __update(options){
		__getByClass("event_container").html("<div style='text-align:center;'>読み込み中...</div>");
		$.getJSON(openpne.apiBase + 'dslevent/search',
			$.extend({end:options.start + (__dtmls * __dates)},params,options),
			function(res){
				if("success" == res.status){
					__render(res.data, options.start);
				}
			}
	  	);
	}
	function __setUrl(tmstart){
		var o_url = $.parse_url(window.location.href);
		o_url.fragments[__id + "_strt"] = tmstart;
		window.location.href = $.serialize_url(o_url);
	}
	var __last_start = null;
	function __updateFromUrl(){
		var o_url = $.parse_url(window.location.href);
		var new_time = o_url.fragments[__id + "_strt"];
		__last_start = moment(new_time ? new Date(1 * new_time) : new Date()).sod().toDate().getTime();
		__update({
			start:__last_start
		});
	}

	__getByClass("event_prev").click(function(){
		__setUrl(__last_start - __dtmls * __dates);
	});
	__getByClass("event_next").click(function(){
		__setUrl(__last_start + __dtmls * __dates);
	});

	$(window).on("hashchange", function(e){
		__updateFromUrl();
	});

	__updateFromUrl();
});

</script>
