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
use_helper('Javascript', 'opUtil', 'opAsset');
use_stylesheet('/opMICGadgetsPlugin/css/timeline_override.css', 'last');
use_javascript('/opMICGadgetsPlugin/js/jq.ltx.youtube.js', 'last');
include("_overrideTimeline_common.php");
include_component("stats", "embedVislinkTimeline");
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	//add caption for upload photo button
	$("#timeline-upload-photo-button").attr("title", "このボタンをクリックし、写真として添付するファイルを選びます").append("<span class='timeline_caption_upload_photo'>写真を添付（" + fileMaxSizeInfo.format + "まで）</span>");
	// fix design
	var tl_header = $(".dparts.homeAllTimeline .partsHeading h3");
	tl_header.html(tl_header.html().replace(/SNSメンバー全員/g, "みんな"))
	 .append($("<span/>",{
		"class": "me_timeline_vis_link"
	}));
	
	// fix youtube embed and link
	var domNodeInsertedHandler = function() {
		$(this).unbind("DOMNodeInserted");
		// fix iframe src attribute
		// remove protocol to avoid secutiry restriction when embedding http frame in https page
		$(".timeline-post-body div iframe").filter(function(){
			return "true" != $(this).attr("_youtube_fix");
		}).each(function(){
			$(this).attr("_youtube_fix", "true");
			$(this).dsl_src_protocol_fix();
		});
		// embed youtube iframe instead of link
		$(".timeline-post-comment-body a").filter(function(){
			return "true" != $(this).attr("_youtube_fix");
		}).each(function(){
			$(this).attr("_youtube_fix", "true");
			$(this).dsl_youtube_link_replace();
		});
		$(this).bind("DOMNodeInserted", domNodeInsertedHandler);
	}
	$('#timeline-list').bind("DOMNodeInserted", domNodeInsertedHandler);
});
//]]>
</script>