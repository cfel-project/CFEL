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
use_helper('opAsset');
//op_smt_use_javascript("/opMICGadgetsPlugin/js/jq.event_fix.js", "last");
?>
<script type="text/javascript">
//<![CDATA[
$("<script type='text/javascript' src='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/js/jq.event_fix.js" ?>'>").appendTo("head");
$(document).ready(function(){
	function __show_req_status_message(text){
		var message = $("#req_status_message");
		if(!message.length){
			message = $("<div id='req_status_message' style='position:absolute;right:.5em;padding: 0 .5em;' class='messageBox dark'/>").appendTo($("div#face")).css({top:Math.floor($("div#show .row.body").offset().top) + "px"}).hide();
		}
		message.html(text).slideDown("slow", function(){var tmp = $(this);setTimeout(function(){tmp.slideUp("slow");}, 2000);});
	}

	var tmpl = $("script#eventEntry");
	if(tmpl.length){
<?php if(true){?>
		tmpl.html(tmpl.html()
			.replace(/<button class=" btn btn-primary btn-mini comment-button " id="postCancel">参加をキャンセルする<\/button>/,'<button class=" btn btn-danger btn-mini comment-button " id="postCancel">参加をキャンセルする</button>')
			.replace(/コメントのみ書き込む/g, "コメントを書き込む")
		);
		$("script#eventComment").each(function(){
			$(this).html($(this).html().replace(/class="deleteComment" data-comment-id="\${id}"><i class="icon-remove"><\/i>/, 'class="deleteComment" data-comment-id="${id}">削除する'));
		});
		function overrideHandler(selector, handler){
			var evobj = $($(document).data("events").click).filter(function(){return selector == this.selector;});
			if(evobj.length > 0){
				evobj[0].handler = handler;
			}
		}
		function __process_joincancel(params, msg){
			var comment = ($("input#commentBody").val() || "").trim();
			if(comment){
				$("input[name=submit]").attr("disabled", true);
				$.post([openpne.apiBase, "event_comment/post.json"].join(""),
					{apiKey:openpne.apiKey, community_event_id:event_id, body:comment},
					"json")
				 .success(function(res){
					$("comments").append($("eventComment").tmpl(res.data,{
						calcTimeAgo: function(){
							return _timeAgo(this.data.created_at);
						}
					}));
					$("input#commentBody").val("");
					__post_joincancel(params, msg);
				})
				 .error(function(res){
					__show_req_status_message("エラー:" + res);
					$("input[name=submit]").removeAttr("disabled");
				});
			}else{
				__post_joincancel(params, msg);
			}
		}
		function __post_joincancel(params, msg){
			$("input[name=submit]").attr("disabled", true);
			$.post([openpne.apiBase, "event/join.json"].join(""),
				$.extend({apiKey: openpne.apiKey, id: event_id}, params || {}),
				"json")
			 .success(function(res){
				__show_req_status_message(msg || "登録されました");
				getEntry();
				$("input[name=submit]").removeAttr("disabled");
			})
			 .error(function(res){
				__show_req_status_message("エラー:" + res);
				console.log(res);
				$("input[name=submit]").removeAttr("disabled");
			});
		}
		overrideHandler("#postJoin", function(){
			__process_joincancel(null, "参加登録されました");
		});
		overrideHandler("#postCancel", function(){
			__process_joincancel({leave: true}, "キャンセルされました");
		});
<?php }else{?>
		function overridePostHandler(selector,def_msg){
			var evobj = $($(document).data("events").click).filter(function(){return selector == this.selector;});
			if(evobj.length > 0){
				var orgh = evobj[0].handler;
				evobj[0].handler = function(){
					if (0 >= $.trim($('input#commentBody').val()).length){
						$('input#commentBody').val(def_msg);
					}
					//add comment change lisnter to handle when request is successfully processed
					$("div#show div#comments").on("DOMSubtreeModified", function(){
						$(this).off("DOMSubtreeModified");
						__show_req_status_message("登録されました");
					});
					orgh();
				};
			}
		}
		overridePostHandler("#postJoin", "参加します");
		overridePostHandler("#postCancel", "取りやめます");
<?php }?>
		$("body>ul.footer>li:nth-child(2)").hide();
		var link_list = $("body>ul.footer>li:nth-child(1)>a");
		if(0 < link_list.length){
			link_list.attr("href", link_list.attr("href").replace(/\/communityEvent\//,"/dslevent/"));
		}

		tmpl.html(tmpl.html()
		 .replace(/開催場所/g, "予約区分")
		 .replace(/class="span3">開催日時/,"class=\"span3 dsl_open_date\">開催日時") + "<span class='dsl_eventshow_fix' style='display:none;'></span>");
		$("div#show").bind("DOMNodeInserted", function(){
			if(0 == $(this).find(".dsl_eventshow_fix").length)
				return;
			$(this).unbind("DOMNodeInserted");
			var f_opdt = $(this).find(".row .dsl_open_date ~ .span9");
			if(0 < f_opdt.length && f_opdt.text()){
				var open_date = moment(f_opdt.text()
				 .replace(/\D/g,"/").replace(/\/$/,"")).toDate();
				link_list.attr("href",link_list.attr("href") + "#eventlist__strt=" + open_date.getTime());
			}
			//bug fix for communityEvent/search.json api in opCommunityTopicPlugin
			//edit/delete button expects "editable" property being set from the api
			//, but that property never be set. //searchSuccess.php.
			if(event_id && 0 == $(this).find(".row .btn-group.span3 a").length){
				var fixed = $("#page_communityEvent_smtShow ._fix_for_event_editable");
				if(!$("#page_communityEvent_smtShow").hasClass("_event_editable_fixed")){
					$("#page_communityEvent_smtShow").addClass("_event_editable_fixed");
					$.getJSON(openpne.apiBase + "dslevent/getEditLink",{
							id: event_id,
							apiKey: openpne.apiKey
						},
						function(res){
							if(res && res.url){
								var t_container = $("#page_communityEvent_smtShow .row:has(>h3.span12)");
								t_container.append("<div class='btn-group' style='display:inline-block; float:right;'><a href='" + res.url + "' class='btn'><i class='icon-pencil'></i></a><a href='javascript:void (0)' class='btn' id='shareEntry'><i class='icon-envelope'></i></a><a href='javascript:void (0)' class='btn' id='deleteEntry'><i class='icon-remove'></i></a></div>")
								.find("h3.span12").css("width", "auto");
								t_container.find("#shareEntry")
								 .dsl_event_notify({data:{
									"eid":"<?php echo $sendParam['eventId']?>",
									"cid":"<?php echo $sendParam['commId']?>"
									},
									success: function(data){
										__show_req_status_message("送信されました");
									},
									error: function(msg, err){
										__show_req_status_message("送信に失敗しました");
									}
								});
							}
						}
					);
				}
			}
		});
		//partially fixing the original error event/delete.json is actually 404
		//, we also need to override the page that is used for successful response.
		$("#deleteEntryModal .modal-button").click(function(e){
			if("execute" == e.target.id){
				$.post(openpne.apiBase + "dslevent/delete",{
					apiKey: openpne.apiKey,
					id: event_id
				},"json")
				 .success(function(res){
					window.location = "<?php echo url_for("dslevent/listCommunity")?>/" + res.data.community_id;
				}).error(function(res){
					console.log(res);
				});
			}else{
				$("#deleteEntryModal").modal("hide");
			}
		});
	}
});
//]]>
</script>