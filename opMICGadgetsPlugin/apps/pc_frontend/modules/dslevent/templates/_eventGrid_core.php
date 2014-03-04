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
<script id="eg_date_cell_templ" type="text/x-jquery-tmpl">
	<div class="date_cell ${optcls}" style="${$item.optpos($item.data)}width:${$item.cellWidth()}px;"{{if events.length}} click_to_show_events_strt="${tm_start}"{{/if}}><div align="center" class="date_indicator">${datestr}</div>{{tmpl($item.data.events) "#eg_event_elem_templ"}}</div>
</script>
<script id="eg_event_elem_templ" type="text/x-jquery-tmpl">
	<div class="event_elem{{if is_event_member}} attend{{/if}}">${name}</div>
</script>
<script type="text/javascript">
$(document).ready(function(){

	var __id = "eventgrid_<?php echo $gadget->id ?>";

	var __double = <?php echo (2 == $gadget->getConfig('rows') ? "true" : "false"); ?>;
	var __horizontal = <?php echo ("horizontal" == $gadget->getConfig('order') ? "true" : "false"); ?>;
	var __dates = <?php echo $gadget->getConfig('cells'); ?>;
	if(__double){
		__getByClass("event_container_row").addClass("double");
	}
	var __dtmls = 86400000
	var __url_dslevent_list = "<?php echo url_for("dslevent/list")."?tky=".$targetKey."&tid=".$targetId ?>";

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
		var idx = 0;
		while(cur < end){
			var cm = moment(new Date(cur));
			items.push({
				tm_start: cm.toDate().getTime(),
				datestr: cm.format("M/D(dd)"),
				optcls: [
					cur == __today ? "today" : (cur > __today ? "future" : "past"),
					"dow_" + cm.day()].join(" "),
				events:[]
			});
			cur += __dtmls;
			idx ++;
		}
		var first_half =  Math.ceil(items.length / 2);
		var c_width = Math.floor(
			__double ? 
			(__getByClass("event_container_row").width() / first_half)
			: (__getByClass("event_container_row").width() - __getByClass("event_prev").outerWidth(true) - __getByClass("event_next").outerWidth(true)) / items.length
		);
		$.each(data, function(i, val){
			var dt = moment(val.open_date, 'YYYY-MM-DD HH:mm:ss').toDate().getTime();
			if(start <= dt && end > dt){
				var idx = (dt - start) / 86400000;
				items[idx].events.push(val);
			}
		});
		var container = __getByClass("event_container")
			.html($("#eg_date_cell_templ")
				.tmpl(items, {
					optpos:function(item){
						var idx = $.inArray(item, items);
						return __double ? "left:" + ( 
							__horizontal ? ((idx % first_half) * c_width + (idx >= first_half ? c_width / 2 :0) ): (idx * c_width / 2)
						) + "px;" : "";
					},
					cellWidth:function(){
						return c_width;
					}
				})
			);
		container.find("[click_to_show_events_strt]")
			.addClass("dsl_clickable")
			.click(function(){
				window.location.href = [
					__url_dslevent_list,
					"#eventlist__strt=",
					$(this).attr("click_to_show_events_strt")
				].join("");
			});

		var cells = container.find(".date_cell");
		cells.first().addClass("first");
		cells.last().addClass("last");
		if(__double){
			var row_2 = container.find(
				".date_cell:nth-child(" + (__horizontal  ? ("n+" + (first_half+1))  : "even") + ")").addClass("row_2");
			row_2.first().addClass("first");
			row_2.last().addClass("last");
		}
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
