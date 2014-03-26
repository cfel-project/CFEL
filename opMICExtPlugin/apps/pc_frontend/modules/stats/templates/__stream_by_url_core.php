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
$data = array();
if(count($results)){
	foreach($results as $result){
		$data[] = array(
			"url"=> $result["url"],
			"time" => strtotime($result["date"]),
			"count" => $result["count"],
		);
	}
}

?>
<style type="text/css">
.axis path, .axis line{
	fill:none;
	stroke: #000;
	shape-rendering: crispEdges;
}
</style>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "logstat_<?php echo $gadget->id ?>";
	var __last_data = <?php echo json_encode($data)?>;

	var container = $("#" + __id + " .logstat_container");
	var margin = {top:40, right: 40, bottom: 20, left: 40},
	width = container.width() - margin.left - margin.right,
	height = 400 - margin.top - margin.bottom,
	stack = d3.layout.stack().offset("silhouette");

	container.empty();
	var __time_min = null;var __time_max = null;
	if(__last_data.length){
		var __map = {};
		var n = 0;
		__time_min = __last_data[0]["time"];
		__time_max = __last_data[__last_data.length - 1]["time"];
		__last_data.forEach(function(d, i){
			var key = d["url"].replace(/^(http|https):\/\/[^/]*/,"").replace(/\?.*$/,"");
			if(!__map[key]){
				 __map[key] = {};
				n++;
			}
			__map[key]["" + Math.floor((d["time"] - __time_min) / 86400)] = (__map[key]["" + Math.floor((d["time"] - __time_min) / 86400)] || 0) + 1 * d["count"];
		});
		var m = (__time_max - __time_min) / 86400 + 1;

		var data = [];
		for(var k in __map){
			data.push(d3.range(m).map(function(d, i){
				return {x: i, y: __map[k]["" + i] || 0};
			}));
		}
		var data_stack = stack(data);

		var x = d3.scale.linear()
			.domain([0, m - 1])
			.range([0, width]);

		var y = d3.scale.linear()
			.domain([0, d3.max(data_stack, function(layer) { return d3.max(layer, function(d) { return d.y0 + d.y; }); })])
			.range([height, 0]);

		var color = d3.scale.linear()
			.range(["#aad", "#556"]);

		var area = d3.svg.area()
			.x(function(d){ return x(d.x);})
			.y0(function(d){ return y(d.y0);})
			.y1(function(d){ return y(d.y0 + d.y);});

		var svg = d3.select("#" + __id + " .logstat_container").append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

		svg.selectAll("path")
			.data(data_stack)
			.enter().append("path")
				.attr("d", area)
				.style("fill", function(){ return color(Math.random());});
	}

/*
	$.getJSON(openpne.apiBase + "dsls/pageLog",{
		apiKey: openpne.apiKey,
		limit: -1
	})
	 .success(function(json){
		__update(json.data);
	})
	 .error(function(xhr, message, error){
		$("#" + __id + " .cnfrm_member_list_container").empty().html("エラー: "+message + " - " + error);
	});
*/
});
//]]>
</script>