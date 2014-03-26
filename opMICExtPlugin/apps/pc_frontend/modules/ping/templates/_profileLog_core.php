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
if(isset($logs[0]["id"])){
	foreach($logs as $log){
		$data[] = array(
			"id" => $log->getId(),
			"value" => $log->getValue(),
			"time" => strtotime($log->getUpdatedAt()),
		);
	}
}
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "profile_ping_log_<?php echo $gadget->id ?>";
	var __logs = <?php echo json_encode($data)?>;
	if(__logs.length > 0){
		$("#" + __id + " .profile_last_confirmation").html(moment(1000 * __logs[0].time).fromNow() + "に " + (__logs[0].value > 0 ? "<span class='btn-primary'>元気</span>" : "<span class='btn-danger'>調子が悪い</span>") + " と答えています");

		$("#" + __id + " .profile_confirm_stats_title").html("過去一週間の様子(返答:" + __logs.length + ")");
		var stats_container = $("#" + __id + " .profile_confirm_stats");
		var width = stats_container.width();
		stats_container.height(width);
		var arc = d3.svg.arc()
			.outerRadius(width/2 - 10)
			.innerRadius(0);

		var pie = d3.layout.pie()
			.sort(null)
			.value(function(d){
				return d.count;
			});

		var __labelmap = {"-1": "調子が悪い", "1": "元気"};
		var __colormap = {"調子が悪い": "#da4f49", "元気": "#006dcc"};
		var _tmp = {};
		__logs.forEach(function(d){
			var label = __labelmap["" + d.value];
			var entry = _tmp[label];
			if(!entry){
				_tmp[label] = 0;
			}
			_tmp[label] ++;
		});
		var data = [];
		for(var k in _tmp){
			data.push({"label": k, "count": _tmp[k]});
		}
		var svg = d3.select("#" + __id + " .profile_confirm_stats")
			.append("svg")
			.attr("width",width)
			.attr("height", width)
			.append("g")
			.attr("transform", "translate(" + (width /2) + "," + (width/2) + ")");

		var g = svg.selectAll(".arc")
			.data(pie(data))
			.enter().append("g")
			 .attr("class", "arc");
		g.append("path")
			.attr("d", arc)
			.style("fill", function(d){
				return d3.rgb(__colormap[d.data.label]);
			});

		g.append("text").transition().duration(1000)
			.attr("transform", function(d){
				return "translate(" + arc.centroid(d) + ")";
			})
			.attr("dy", ".35em")
			.style("text-anchor", "middle")
			.style("fill", "white")
			.text(function(d){
				return d.data.label;
			});
			
	}else{
		$("#" + __id + " .profile_last_confirmation").html("まだ記録がありません");
	}
});
//]]>
</script>
