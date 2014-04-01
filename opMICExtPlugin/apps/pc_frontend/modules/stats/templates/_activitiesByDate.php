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
use_javascript('/opMICExtPlugin/js/jq.actdts.gr.js', 'last');
?>
<style type="text/css">
.area {
  fill: steelblue;
  clip-path: url(#clip);
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

<div class="dparts activity_by_date" id="activity_by_date_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>つぶやき数の推移</h3>
		</div>
		<div class="logstat_container">
			読み込み中...
		</div>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "activity_by_date_<?php echo $gadget->id ?>";
	var __params = $.extend({},{apiKey: openpne.apiKey},<?php echo htmlspecialchars_decode($prmsjson)?>);

	$.getJSON(openpne.apiBase + "stats/activitiesByDate",
		__params)
	 .success(function(json){
		if(json.num && json.data){
			$("#" + __id + " .logstat_container")
			 .css("height", "8em")
			 .actdts_ar_graph(json.data,{
				dt_range:[new Date()]/*,
				on_brush: function(extent){
					console.log(extent);
				}*/
			});
		}else{
			$("#" + __id + " .logstat_container").empty().html("データがありません");
		}
	})
	 .error(function(xhr, message, error){
		$("#" + __id + " .logstat_container").empty().html("エラー: "+message + " - " + error);
	});

});


//]]>
</script>
