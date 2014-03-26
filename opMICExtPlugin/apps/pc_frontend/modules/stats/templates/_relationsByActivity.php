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
use_stylesheet('/opMICExtPlugin/css/stats.css', 'last');
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
use_javascript(opMICExtConfig::getD3URL(), 'last', array(
	"raw_name" => true,
));
use_javascript('/opMICExtPlugin/js/jq.actrels.gr.js', 'last');
?>
<style type="text/css">
.link {
	stroke: #ccc;
}

.node text {
	pointer-events: none;
	font: 10px sans-serif;
}
.node image:hover{
	cursor:pointer;
}
.rel_by_activity .slider{
	height: 1em;
}

</style>

<div class="dparts rel_by_activity" id="rel_by_activity_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>つぶやきでのつながり</h3>
		</div>
		<div class="logstat_container">
			読み込み中...
		</div>
	</div>
</div>

<?php 
$nodes = array();
if(count($members)){
	foreach($members as $member){
		$nodes[] = array(
			"id" => $member["id"],
			"count" => $member["count"],
			"name" => $member["name"],
			"image" => (empty($member["image"]) ? op_image_path("no_image.gif", true) : sf_image_path($member["image"], array("size" => "48x48"))),
			"prof_url" => app_url_for('pc_frontend', array('sf_route' => 'obj_member_profile', 'id' => $member["id"]), true),
		);
	}
}
$links = array();
if(count($relations)){
	foreach($relations as $relation){
		$links[] = array(
			"src" => $relation["src"],
			"trg" => $relation["trg"],
			"count" => $relation["count"],
		);
	}
}
$data = array(
	"nodes" => $nodes,
	"links" => $links,
);
?>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "rel_by_activity_<?php echo $gadget->id ?>";
	var __data = <?php echo json_encode($data)?>;

	$("#" + __id + " .logstat_container")
	 .css("height", "600px")
	 .actrel_fr_graph(__data);

});


//]]>
</script>
