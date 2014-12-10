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
use_helper("Javascript", "opUtil", "opAsset", "opConfirmationLog");
op_smt_use_stylesheet('/opMICExtPlugin/css/stats.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
op_smt_use_javascript(opMICExtConfig::getD3URL(), 'last', array(
	"raw_name" => true,
));
op_smt_use_javascript('/opMICExtPlugin/js/jq.actrels.gr.js', 'last');
op_smt_use_javascript('/opMICExtPlugin/js/jq.actdts.gr.js', 'last');
op_smt_use_javascript('/opMICExtPlugin/js/jq.loading.scr.js', 'last');
?>
<style type="text/css">
.link {
	stroke: #ccc;
}

.node text {
	pointer-events: none;
/*	font: 10px sans-serif;*/
}
.node image:hover{
	cursor:pointer;
}
.rel_by_activity .slider{
	height: 1em;
}

</style>
<style type="text/css">
.area {
  fill: steelblue;
  clip-path: url(#clip);
}

.axis{
	font-size:8px;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.brush .extent {
  stroke: #fff;
  fill-opacity: .125;
  shape-rendering: crispEdges;
}

</style>

<div class="dparts rel_by_activity" id="rel_by_activity_<?php echo $gadget->id ?>">
	<div class="row">
		<div class="gadget_header span12">
			<span>つぶやきでのつながり</span>
			<select style="margin:0;padding:0;width:6.5em;" class="duration">
				<option selected="true" value="90d">90日分</option>
				<option value="183d">半年分</option>
				<option value="1y">一年分</option>
				<option value="all">全て</option>
			</select>
		</div>
	</div>
	<div class="logstat_container">
		読み込み中...
	</div>
	<div class="act_by_date_container">
		読み込み中...
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "rel_by_activity_<?php echo $gadget->id ?>";
	var __params = $.extend({},{apiKey: openpne.apiKey},<?php echo htmlspecialchars_decode($prmsjson)?>);

	var def_duration = "<?php echo $def_duration ?>";
	if(def_duration){
		$("#" + __id + " select.duration").val(def_duration);
	}
	var _rel_graph = null;
	var _in_flight = false;
	function __update_rel_graph(prms){
		_in_flight = true;
		$("body").show_loading_screen();
		$.getJSON(openpne.apiBase + "stats/activityRelations",
			$.extend({}, __params, prms))
		 .success(function(json){
			_in_flight = false;
			$("body").hide_loading_screen();
			if(!_rel_graph){
				_rel_graph = $("#" + __id + " .logstat_container")
				 .css("height", ($(window).height() - $("#" + __id + " .gadget_header").offset().top - $("#" + __id + " .gadget_header").height() -60) + "px")
				 .actrel_fr_graph({data: json});
			}else{
				_rel_graph.actrel_fr_graph_update(json);
			}
		})
		 .error(function(xhr, message, error){
			_in_flight = false;
			$("body").hide_loading_screen();
			$("#" + __id + " .logstat_container").empty().html("エラー: "+message + " - " + error);
		});
	}
	var __map_start = {
		"90d": (new Date((new Date()).getTime() - 86400000 * 90)).getTime(),
		"183d": (new Date((new Date()).getTime() - 86400000 * 183)).getTime(),
		"1y": (new Date((new Date()).getTime() - 86400000 * 365)).getTime(),
		"all": undefined
	};
	$("#" + __id + " select.duration").change(function(){
//hack android chrome does not refresh the visible value on select element.
		var self = $(this);
		var val = self.val();
		self.find("option[value='" + val + "']").prop("selected", true);
		self.find("option[value!='" + val + "']").prop("selected", false);
		setTimeout(function(){
			self.offset({top:self.offset().top});
		},100);
//hack
		__start();
	});
function __start(){
	__params.start =  __map_start[$("#" + __id + " select.duration").val()];
	__update_rel_graph();

	var _brush_timeout = null;
	$.getJSON(openpne.apiBase + "stats/activitiesByDate",
		__params)
	 .success(function(json){
		if(json.num && json.data){
			$("#" + __id + " .act_by_date_container")
			 .css("height", "60px")
			 .actdts_ar_graph(json.data,{
				margin: {left:5,right:5, bottom:18},
				tickSize: 2,
				no_yaxis: true,
				dt_range:[new Date()],
				on_brush: function(extent){
					if(_rel_graph && !_in_flight){
						if(_brush_timeout){
							clearTimeout(_brush_timeout);
							_brush_timeout = null;
						}
						_brush_timeout = setTimeout(function(){
							_brush_timeout = null;
							__update_rel_graph(extent[0].getTime() < extent[1].getTime() ? {
								start: extent[0].getTime(),
								end: extent[1].getTime()
							} : undefined);
						},400);
					}
				}
			});
		}else{
			$("#" + __id + " .act_by_date_container").empty().html("データがありません");
		}
	})
	 .error(function(xhr, message, error){
		$("#" + __id + " .act_by_date_container").empty().html("エラー: "+message + " - " + error);
	});
}
	__start();
});


//]]>
</script>
