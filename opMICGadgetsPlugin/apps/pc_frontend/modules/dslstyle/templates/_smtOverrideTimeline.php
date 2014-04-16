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
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	// fix design
	var tl_header = $("div.timeline").prev("div.row:has(.gadget_header)").find(".gadget_header");
	tl_header.html(tl_header.html().replace(/SNS全体/g, "みんな"));
	
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
						$("#timeline-post-" + tid).remove();
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
		
		// add link to delete timeline post, and timeline comment
		$("div.timeline-post", $(this)).filter(function(){
			return "true" != $(this).attr("_delete_fix");
		}).each(function(){
			$(this).attr("_delete_fix", "true");
			
			var addComment = $(this).find("div.timeline-post-control a.timeline-comment-link");
			if (addComment.attr("href")) {
				var tid = addComment.attr("href").substring(10); // href is "#timeline-{tid}"
				
				// add link to delete timeline post
				var member = $(this).find("div.timeline-post-member-image a");
				if (member.attr("href")) {
					var mpaths = member.attr("href").split("/");
					var mid = mpaths[mpaths.length-1];
					if (mid=="<?php echo $u_id ?>") {
						$(this).attr("id", "timeline-post-" + tid);
						
						addComment.after("<span> | </span><a href=\"#timeline-post-delete-confirm-"+ tid + "\" class=\"timeline-post-delete-confirm-link\">削除する</a>");
						$("a.timeline-post-delete-confirm-link", $(this)).click(function(e){
							$('#deleteContentModal').attr('data-timeline-id', tid).on('shown', function(e){
								showModal($(this));
								return this;
							}).modal('show');
							
							e.preventDefault();
							return false;
						});
					}
				}
				
				// add link to delete timeline comment
				if (<?php echo $comment_delete_cfg?>) {
					$("div.timeline-post-comments", $(this)).filter(function(){
						return "true" != $(this).attr("_delete_fix");
					}).each(function(){
						$(this).attr("_delete_fix", "true");
						
						var comments = new Array();
						var updateComment = function(commentNode, comment) {
							if (comment.member.id=="<?php echo $u_id ?>") {
								commentNode.attr("id", "timeline-post-" + comment.id);
								
								var commentBody = commentNode.find("div.timeline-post-comment-body");
								commentBody.after("<div class=\"timeline-post-control\"><a href=\"#timeline-post-comment-delete-confirm-"+ comment.id + "\" class=\"timeline-post-comment-delete-confirm-link\">削除する</a></div>");
								$("a.timeline-post-comment-delete-confirm-link", commentNode).click(function(e){
									$('#deleteContentModal').attr('data-timeline-id', comment.id).on('shown', function(e){
										showModal($(this));
										return this;
									}).modal('show');
									
									e.preventDefault();
									return false;
								});
							}
						}
						var commentNodeInsertedHandler = function() {
							var _this = $(this);
							setTimeout(function(){
								_this.unbind("DOMNodeInserted");
								
								$("div.timeline-post-comment", _this).each(function(index){
									var commentNode = $(this);
									if ("true" != commentNode.attr("_delete_fix")) {
										commentNode.attr("_delete_fix", "true");
										
										if (index > comments.length-1) {
											$.ajax({
												async: false,
												type: 'GET',
												url: openpne.apiBase + 'activity/commentSearch.json?apiKey=' + openpne.apiKey,
												data: {
													'timeline_id': tid,
													'count': comments.length + 20
												},
												success: function(json){
													comments = json.data;
													updateComment(commentNode, comments[index]);
												},
												error: function(XMLHttpRequest, textStatus, errorThrown){
												}
											});
										} else {
											updateComment(commentNode, comments[index]);
										}
									}
								});
								
								_this.bind("DOMNodeInserted", commentNodeInsertedHandler);
							}, 1000);
						}
						$(this).bind("DOMNodeInserted", commentNodeInsertedHandler);
					});
				}
			}
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