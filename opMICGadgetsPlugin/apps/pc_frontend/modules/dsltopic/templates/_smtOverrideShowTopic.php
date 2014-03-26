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
if(!empty($communityTopic)){
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("script#topicEntry").each(function(){
		$(this).html($(this).html().replace(/\/communityTopic\/edit\/\$\{id\}/g, "/d_topic/edit/${id}")
<?php if(!empty($pdf_url) && !empty($pdf_name)){ ?>
		.replace(/\<div class="row images center"\>/, "<div class='row pdf'><a target='_blank' href='<?php echo $pdf_url ?>'><?php echo $pdf_name?></a></div><div class=\"row images center\">")
<?php } ?>
		);
	});
	$("script#topicComment").each(function(){
		$(this).html($(this).html().replace(/class="deleteComment" data-comment-id="\${id}"><i class="icon-remove"><\/i>/, 'class="deleteComment" data-comment-id="${id}">削除する'));
	});

	$("body>ul.footer>li:nth-child(2)").hide();
	var link_list = $("body>ul.footer>li:nth-child(1)>a");
	if(0 < link_list.length){
		link_list.attr("href", link_list.attr("href").replace(/\/communityTopic\//,"/d_topic/"));
	}
	//fix:the url after removal is not correct.
	var evobj = $("#deleteEntryModal .modal-button#execute").data("events").click;
	if(evobj.length > 0){
		evobj[0].handler = function(e){
			if("execute" == e.target.id){
				$.post(openpne.apiBase + "dsltopic/delete",//"topic/delete.json",
				{apiKey: openpne.apiKey, id: topic_id},
				"json")
				 .success(function(res){
					window.location = "<?php echo url_for("d_topic/listCommunity")?>/" + res.data.community_id;//change first path for main stream.
				})
				 .error(function(res){
					if(window["console"]){
						console.log(res);
					}
				});
			}else{
				$("#deleteEntryModal").modal("hide");
			}
		};
	}

});
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
$("<script type='text/javascript' src='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/js/jq.ltx.youtube.js" ?>'>").appendTo("head");

$(document).ready(function(){
	var domNodeInsertedHandler = function() {
		$(this).unbind("DOMNodeInserted");
		$(this).find(".row.body, .comment-body").filter(function(){
			return "true" != $(this).attr("_link_wrap");
		}).each(function(){
			$(this).attr("_link_wrap", "true");

			$(this).dsl_url_text_replace();
			var width = $(this).width();
			$(this).find("a").each(function(){
				$(this).dsl_youtube_link_replace({width:width});
			});
		});
		$(this).bind("DOMNodeInserted", domNodeInsertedHandler);
	};
	$('div#show').bind("DOMNodeInserted", domNodeInsertedHandler);
});
//]]>
</script>
<?php
}
?>