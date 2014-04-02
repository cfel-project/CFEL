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
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
use_javascript(opMICExtConfig::getD3URL(), 'last', array(
	"raw_name" => true,
));
use_javascript('/opMICExtPlugin/js/jq.stgrbar.gr.js', 'last');
use_javascript('/opMICExtPlugin/js/jq.actdts.gr.js', 'last');
?>
<style type="text/css">
.axis path, .axis line{
	fill:none;
	stroke: #000;
	shape-rendering: crispEdges;
}
</style>

<div class="dparts" id="bargraph_by_event_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>日毎の活動状況</h3>
		</div>
		<label><input type="radio" name="mode" value="grouped"> Grouped</label>
		<label><input type="radio" name="mode" value="stacked" checked> Stacked</label>
		<label><input type="checkbox" name="loadonly"> Load Only</label>
		<label><select class="cluster_option">
		</select></label>
		<div class="logstat_container">
			読み込み中...
		</div>
		<div class="context_container">
		</div>
	</div>
</div>
<?php
$data = array();
if(count($results)){
	$regexp = "/^(https|http):\/\/[^\/]+/";
	$root = url_for("@homepage");
	foreach($results as $result){
		$data[] = array(
			"member_id" => $result["member_id"],
			"event" => $result["event"],
			"path" => str_replace($root, "/", preg_replace($regexp, "", $result["url"])),
			"time" => strtotime($result["date"]),
			"count" => 1 * $result["count"],
		);
	}
}
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "bargraph_by_event_<?php echo $gadget->id ?>";
	var __org_data = <?php echo json_encode($data)?>;
	var __last_data = __org_data.map(function(d){return d;});

	var __gr_path_topic = ["d_topic", "communityTopic"];
	var __gr_path_event = ["d_event", "dslevent", "communityEvent"];

	var __clusters = $.merge([{
		"id": "by_device",
		"name": "デバイス",
		"map": function(d){
			return d["event"].match(/_smt$/) ? "tablet" : "pc";
		}
	},{
		"id": "by_member",
		"name": "メンバー",
		"map": function(d){
			return d["member_id"];
		}
	},{
		"id": "by_path",
		"name": "ページ",
		"map": function(d){
			var path = d["path"].replace(/^\//,"").replace(/\/.*/,"");
			if(-1 != $.inArray(path, __gr_path_topic)){
				return "topic";
			}else if(-1 != $.inArray(path, __gr_path_event)){
				return "event";
			}else{
				return path;
			}
		}
	}], [<?php echo opMICExtConfig::getBargraphCluserExt() ?>]);

	var __cluster_map = {};
	$.each(__clusters, function(){
		__cluster_map[this.id] = this.map;
		$("#" + __id + " .cluster_option").append([
			"<option value='",
			this.id,
			"'>",
			this.name,
			"</option>"
		].join(""));
	});

	var container = $("#" + __id + " .logstat_container");
	var e_load_only =  $("#" + __id + " input[name='loadonly']").on("change", function(){
		__update();
	});

	var e_clustoer_sel = $("#" + __id + " select.cluster_option").on("change", function(){
		__update();
	});

	var __bar_focus = null;
	function __update(data){
		var __load_only = e_load_only.attr("checked");
		__bar_focus = container.stgr_bar_graph(__last_data = (data || __last_data), {
			color_range:["#2a4073", "#f8b862"],
			margin:{top:40, right:40, bottom:20, left:40},
			height:400,
			fn_filter: __load_only ? function(d){
				return d["event"].match(/^load/);
			} : undefined,
			fn_cluster: __cluster_map[e_clustoer_sel.val()]
		});
		
	}
	__update();

	function _convert_count_by_date(data){
		var __dt_map = {};
		data.forEach(function(d){
			if(!__dt_map["" + d.time]){
				__dt_map["" + d.time] = 0;
			}
			__dt_map["" + d.time] += d.count;
		});
		var ret = [];
		for(var k in __dt_map){
			ret.push({
				time: 1 * k,
				count: 1 * __dt_map[k]
			});
		}
		return ret;
	}
	function _load_context(data){
		$("#" + __id + " .context_container")
			.actdts_ar_graph(_convert_count_by_date(data),{
				dt_range:[moment().eod().toDate()],
				no_yaxis: true,
				margin:{top:10, right:40, bottom:20, left:65},
				height: 80,
				on_brush: function(extent){
					if(extent && extent.length > 1){
						var __tm_min = extent[0].getTime() / 1000, __tm_max = extent[1].getTime() / 1000;
						if(__tm_min < __tm_max){
							__update(__org_data.filter(function(d){
								return d["time"] >= __tm_min && d["time"] <= __tm_max;
							}));
						}else{
							__update(__org_data);
						}
					}
				}
			});
	}
	_load_context(__last_data);
});

//]]>
</script>