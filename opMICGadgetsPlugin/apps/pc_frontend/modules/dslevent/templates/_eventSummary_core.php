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
<script id="es_event_elem_tmpl"  type="text/x-jquery-tmpl">
	<div class="row">
		<span class="ev_date">${$item.format_op_date()}</span>
		<span class="ev_date_op">${open_date_comment}</span>
		<span class="ev_body"><a href="${$item.ev_url()}">${name}</a></span>
	</div>
</script>
<script type="text/javascript">
$(document).ready(function(){
	var __id = "eventsummary_<?php echo $gadget->id ?>";
	var __event_base_url = "<?php echo url_for('communityEvent/')?>";
	var __more_url = "<?php echo (empty($moreUrl) ? "" : url_for($moreUrl).(empty($moreUrlPath) ? "" : "/".$moreUrlPath)) ?>";

	function __query(s){
		return $("#" + __id + " " + s);
	}
	var params = {
		target: "<?php echo $targetKey ?>",
		limit: "<?php echo $limit?>",
		apiKey:openpne.apiKey,
		target_id: "<?php echo $targetId ?>"
	};
	var __today = moment(new Date()).sod().toDate().getTime();

	function __render(items, options){
		var container = __query(".container_on_load");
		if(items.length > 0){
			container
			 .empty()
			 .html($("#es_event_elem_tmpl")
				 .tmpl(items, {
					format_op_date: function(){
						return moment(this.data.open_date, 'YYYY-MM-DD HH:mm:ss').format("M/D(dd)");
					},
					ev_url: function(){
						return __event_base_url + this.data.id + "#" + this.data.id;
					}
				 })
			);
		}else{
			container
			 .empty()
			 .html("<div style='text-align:center;'>この先のイベントはありません</div>");
		}
		container.removeClass("container_on_load");
		if(__more_url){
			__set_more_link(__query(".next_link_container"), __more_url);
		}else{
			if(items.length > 0){
				var func_prev = function(){
					__update({
						end: options.start ? options.start : moment(items[0].open_date).toDate().getTime()
					});
				};
				var func_next = function(){
					__update({
						start: options.end ? options.end : moment(items[items.length - 1].open_date).toDate().getTime() + 86400000
					});
				};
				if(options.start){
					__set_more_link(__query(".next_link_container"), func_next);
				}
				if(options.end){
					__set_more_link(__query(".prev_link_container"), func_prev);
				}
				if(0 == __query(".prev_link_container .read_next").length){
					__set_more_link(__query(".prev_link_container"), func_prev);
				}
			}else{
				if(options.start){
					__set_more_link(__query(".next_link_container"), null);
				}
				if(options.end){
					__set_more_link(__query(".prev_link_container"), null);
				}
			}
		}
	}
	function __set_more_link(container, url){
		container.html(
				url ?  "<a class='span11 btn btn-block small read_next' href='"
					 + ("string" == typeof(url) ? url : "javascript:")
					 + "'>もっと読む</a>"
					: ""
			);
		if("function" == typeof(url)){
			container.find(".read_next").click(url);
		}
	}
	function __update(options){
		__query(".event_container")[options.start ? "append" : "prepend"]("<div class='container_on_load'><div style='text-align:center;'>読み込み中...</div></div>");
		$.getJSON(openpne.apiBase + "dslevent/search",
			$.extend({}, params, options),
			function(res){
				if("success" == res.status){
					__render(res.data, options);
				}
			}
		);
	}
	__update({
		start: __today
	});
});
</script>
