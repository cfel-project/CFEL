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
op_smt_use_stylesheet('/opMICGadgetsPlugin/css/timeline_override.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/bootstrap-modal.js', 'last');
op_smt_use_javascript('/opMICGadgetsPlugin/js/jq.ltx.youtube.js', 'last');
include("_overrideTimeline_common.php");
include_component("stats", "smtEmbedVislinkTimeline");
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	// fix design
	var tl_header = $("div.timeline").prev("div.row:has(.gadget_header)").find(".gadget_header");
	tl_header.html(tl_header.html().replace(/SNS全体/g, "みんな"))
	 .append($("<span/>",{
		"class": "me_timeline_vis_link"
	}));
	
	// prepare modal window for deleting timeline
	var showModal = function(modal){
		var windowHeight = window.outerHeight > $(window).height() ? window.outerHeight : $(window).height();
		$('.modal-backdrop').css({'position': 'absolute','top': '0', 'height': windowHeight});
		
		var scrollY = window.scrollY;
		var viewHeight = window.innerHeight ? window.innerHeight : $(window).height();
		var modalTop = scrollY + ((viewHeight - modal.height()) / 2 );
		
		modal.css('top', modalTop);
	}
	$('#deleteContentModal .modal-button').click(function(e){
		if(e.target.id == 'execute'){
			var tid = $("#deleteContentModal").attr('data-timeline-id');
			var params = {
				apiKey: openpne.apiKey,
				activity_id: tid,
			};
			$.post(openpne.apiBase + "activity/delete.json",
					params,
					'json')
					.success(function(res){
						var src = $("a[tl_activity_id=" + tid +"]");
						if(src.closest(".timeline-post-comment").length){
							src.closest(".timeline-post-comment").remove();
						}else{
							src.closest(".timeline-post").remove();
						}
						//$("#timeline-post-" + tid).remove();
					})
					.error(function(res){
						console.log(res);
					})
					.complete(function(res){
						$('#deleteContentModal').attr('data-timeline-id', '').modal('hide');
					});
		} else {
			$('#deleteContentModal').attr('data-timeline-id', '').modal('hide');
		}
	});

	//modify timeline templates to add links for removal here
	$("#timelineTemplate").each(function(){
		$(this).html($(this).html()
			.replace(/class="timeline-comment-link">コメントする<\/a>/,"class=\"timeline-comment-link\">コメントする</a>{{if member.id == '<?php echo $u_id ?>'}}<span> | </span><a tl_activity_id=\"${id}\" href=\"#timeline-post-delete-confirm-${id}\" class=\"timeline-post-delete-confirm-link\">削除する</a>{{/if}}"));
	});
	$("#timelineCommentTemplate").each(function(){
		$(this).html($(this).html()
			.replace(/<\/div>\s$/g, "{{if member.id == '<?php echo $u_id ?>'}}<div class=\"timeline-post-control\"><a tl_activity_id=\"${id}\" href=\"#timeline-post-comment-delete-confirm-${id}\" class=\"timeline-post-comment-delete-confirm-link\">削除する</a></div>{{/if}}</div>"));
	});

	// fix youtube link, add link to delete timeline post
	var domNodeInsertedHandler = function() {
		$(this).unbind("DOMNodeInserted");
		
		// embed youtube iframe instead of link
		$(".timeline-post-body a,.timeline-post-comment-body a", $(this)).filter(function(){
			return "true" != $(this).attr("_youtube_fix");
		}).each(function(){
			$(this).attr("_youtube_fix", "true");
			$(this).dsl_youtube_link_replace();
		});
		//add onclick handler for the deletion links here.
		$("a.timeline-post-delete-confirm-link, a.timeline-post-comment-delete-confirm-link", $(this)).filter(function(){
			return "true" != $(this).attr("_timeline_del_confirm_fix");
		}).each(function(){
			$(this).attr("_timeline_del_confirm_fix", "true")
			.click(function(e){
				$('#deleteContentModal').attr('data-timeline-id', $(this).attr("tl_activity_id")).on('shown', function(e){
					showModal($(this));
					return this;
				}).modal('show');
				
				e.preventDefault();
				return false;
			});
		});

		$(this).bind("DOMNodeInserted", domNodeInsertedHandler);
	}
	$('#timeline-list').bind("DOMNodeInserted", domNodeInsertedHandler);
});
//]]>
</script>
<div class="modal hide" id="deleteContentModal">
  <div class="modal-header">
    <h5>つぶやきの削除</h5>
  </div>
  <div class="modal-body">
    <p class="center">本当にこのつぶやきを削除しますか？</p>
  </div>
  <div class="modal-footer">
    <button class="btn modal-button" id="cancel">キャンセル</button>
    <button class="btn btn-primary modal-button" id="execute">削除</button>
  </div>
</div>