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
 use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
 use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
?>
<script id="memberElement" type="text/x-jquery-tmpl">
	<img title="${member.screen_name}" src="${member.profile_image}" style="width:1em;height:1em;"/>
</script>

<script id="topicEntry" type="text/x-jquery-tmpl">
<div class="dsl_topic dsl_button" topic_id="${id}">
	<span class="root">
		<h4 class="dsl_subject">
			<a target="_blank" href="<?php echo public_path('communityTopic')?>/${id}">
				{{tmpl "#memberElement"}} ${name}
			</a>
		</h4>
		<span class="container_body">
			{{wrap "#summarize"}}
				<span>{{html body}}</span>
			{{/wrap}}
			<span class="dsl_comments"></span>
			<span class="dsl_created">${$item.calcTimeAgo()}</span>
		</span>
	</span>
	<div class="dsl_float_clearfix">.</div>
	<div class="container_comments"></div>
</div>
</script>

<script id="commentEntry" type="text/x-jquery-tmpl">
<div class="dsl_comment dsl_button" comment_id="${id}">
	{{tmpl "#memberElement"}} 
		<span class="dsl_body">
			{{wrap "#summarize"}}
				<span>{{html body}}</span>
			{{/wrap}}
		</span>
	<span class="dsl_created">${$item.calcTimeAgo()}</span>
	<div class="dsl_float_clearfix">.</div>
</div>
</script>

<script id="summarize" type="text/x-jquery-tmpl">
	{{each $item.html("span", true)}}
		<span title="${$value}">
			${$value.substr(0,18)}
			{{if $value.length > 18}}
					...
			{{/if}}
		</span>
	{{/each}}
</script>

<script type="text/javascript">
$(function(){
	var params = {
		apiKey: openpne.apiKey,
//		format: 'mini',
		target: 'community',
		target_id: <?php echo $communityId ?>,
		count: 10
	}
	function __updateComments(elem, topicId){
		$(".dsl_comments", elem).addClass("dslLoading");
		$.getJSON(openpne.apiBase + 'topic_comment/search.json',{
				apiKey: openpne.apiKey,
				community_topic_id: topicId,
				count: 0
			},
			function(res){
				var comments = $(".dsl_comments", elem).removeClass("dslLoading");
				if(res.data.length > 0){
					$(".dsl_comments", elem)
//					 .html("(<a href='javascript:'>" + res.data.length + "</a>)")
					 .html("(回答" + res.data.length + "件)")
/*					 .hover(function(){
						$(".dsl_comment_list", elem).toggle(400);
					})*/;
					var entry = $("#commentEntry").tmpl(res.data, {
						calcTimeAgo: function(){
							return moment(this.data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow();
						}
					});
					var clist = $("<div class='dsl_comment_list'/>").appendTo($(".container_comments", elem)).append(entry).hide();
				}
			}
		);
	}
	var listelem = $("#topicList");
	function __update(){
		listelem.empty();
		listelem.addClass("dslLoading");
		$.getJSON(openpne.apiBase + 'topic/search.json',
			params,
			function(res)
			{
				var loadListenerId = "<?php echo $loadListenerId ?>"
				$("#topicList").removeClass("dslLoading");
				if (res.data.length > 0){
					var entry = $('#topicEntry').tmpl(res.data,{
						calcTimeAgo: function(){
						return moment(this.data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow();
						}
					});
					$('#topicList').append(entry);
					$('#readmore').show();
					entry.click(function(){
						window.open("<?php echo public_path('communityTopic')?>/" + $(this).attr("topic_id"), "_blank");
					});
					entry.each(function(){
						__updateComments($(this), $(this).attr("topic_id"));
					});
				}
				if(loadListenerId){
					$("#" + loadListenerId).resize();
				}
			}
	  	);
	}
	$("#updateButton").click(__update);
	__update();
})
</script>
<div class="dslPartsContainer dsl_mini">
	<div><h3><?php echo $title ?></h3></div>
	<ul class="moreInfo">
		<li style="margin-bottom:2px;float:left;">
			<a target="_blank" href="<?php echo public_path('communityTopic/new').'/'.$communityId ?>">新しいトピックを投稿する</a>
		</li>
		<li style="margin-bottom:2px;padding:0;background:none;float:right;">
			<input id="updateButton" type="button" class="input_submit" value="更新" style="padding:2px;">
		</li>
	</ul>
	<div id="topicList" class="dsl_topiclist" style="margin-left: 0px;">
	</div>
	<ul class="moreInfo">
		<li id="readmore" style="float:right;">
			<a target="_blank" href="<?php echo public_path('community').'/'.$communityId ?>">他のQ&Aトピックを見る</a>
		</li>
	</ul>
</div>
