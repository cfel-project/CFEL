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
			<option value="by_device" selected>デバイス</option>
			<option value="by_member">メンバー</option>
			<option value="by_lecture">講習会</option>
			<option value="by_path">ページ</option>
		</select></label>
		<div class="logstat_container">
			読み込み中...
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
			"count" => $result["count"],
		);
	}
}
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "bargraph_by_event_<?php echo $gadget->id ?>";
	var __last_data = <?php echo json_encode($data)?>;

	var __gr_we = [11,12,13,14,15,16,17,18,21,22,23,27,30,31];
	var __gr_wd = [9,10,13,14,19,20,24,25,26,27,28,29];

	var __cluster_map = {
		"by_device" : function(d){
			return d["event"].match(/_smt$/) ? "tablet" : "pc";
		},
		"by_member": function(d){
			return d["member_id"];
		},
		"by_lecture": function(d){
			var id = 1 * d["member_id"];

			if(1 < id && 9 > id){
				return "ict";
			}else if(59 < id && 78 > id){
				return "2nd";
			}else if(-1 != $.inArray(id, __gr_we) || /*){
				return "weekend";
			}else if(*/ -1 != $.inArray(id, __gr_wd)){
				return "1st";
			}else{
				return "other";
			}
		},
		"by_path": function(d){
			return d["path"].replace(/^\//,"").replace(/\/.*/,"");
		}
	};

	var container = $("#" + __id + " .logstat_container");
	var e_load_only =  $("#" + __id + " input[name='loadonly']").on("change", function(){
		__update();
	});

	var e_clustoer_sel = $("#" + __id + " select.cluster_option").on("change", function(){
		__update();
	});

	function __update(){
		var __load_only = e_load_only.attr("checked");
		container.stgr_bar_graph(__last_data, {
			color_range:["#2a4073", "#f8b862"],
			height:400,
			fn_filter: __load_only ? function(d){
				return d["event"].match(/^load/);
			} : undefined,
			fn_cluster: __cluster_map[e_clustoer_sel.val()]
		});
	}
	__update();

});

//]]>
</script>