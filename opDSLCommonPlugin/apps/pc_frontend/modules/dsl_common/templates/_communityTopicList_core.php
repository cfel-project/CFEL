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
?>
<script id="memberElement" type="text/x-jquery-tmpl">
	<span class="dsl_author" style="background-image:url(${member.profile_image});">${member.screen_name}</span>
</script>
<?php
	$acl = opCommunityTopicAclBuilder::buildCollection($community, array($sf_user->getMember()));
	if(!isset($partId)){
		$partId = 'dsl_comtopic_list_'.$gadget->id;
	}
	if(empty($path_open_entry)){
		$path_open_entry = "communityTopic";
	}
?>
<script id="topicEntry" type="text/x-jquery-tmpl">
<div class="dsl_topic dsl_button" topic_id="${id}">
	<div class="root">
		<h4><a href="<?php echo public_path($path_open_entry)?>/${id}"><span class="dsl_subject">${name}</span></a></h4>
		<div class="inline dsl_author_box">{{tmpl "#memberElement"}}<span class="dsl_author_suffix">さんのトピック</span></div>
		<div class="inline container_body">
			{{wrap(100) "#summarize"}}
				<span>{{html body}}</span>
			{{/wrap}}
		</div>
		<div class="inline dsl_created">
			<div class="inline">${$item.calcTimeAgo()}</div>
			<div class="inline dsl_comments" style="min-width:16px;">&nbsp;</div>
		</div>
		
		<div class="dsl_float_clearfix">.</div>
	</div>
	<div class="container_comments">
	</div>
	<ul class="moreInfo">
		<li class="dsl_screenreader_only" style="float:right;">
			<a href="<?php echo public_path($path_open_entry)?>/${id}">このトピックを全部見る・コメントする</a>
		</li>
	</ul>
</div>
</script>

<script id="commentEntry" type="text/x-jquery-tmpl">
<div class="dsl_comment dsl_button" comment_id="${id}">
	<div class="inline dsl_author_box">{{tmpl "#memberElement"}}<span class="dsl_author_suffix">さんより</span></div>
	<div class="inline dsl_created">${$item.calcTimeAgo()}</div>
	<div class="inline container_body">
		{{wrap(50) "#summarize"}}
			<span>{{html body}}</span>
		{{/wrap}}
	</div>
	<div class="dsl_float_clearfix">.</div>
</div>
</script>
<script id="summarize" type="text/x-jquery-tmpl">
	{{each $item.html("span", true)}}
		<span title="${value}">
			${$value.substr(0,$data)}
			{{if $value.length>$data}}
					...
			{{/if}}
		</span>
	{{/each}}
</script>
<script type="text/javascript">
$(function(){
	var per_page = <?php echo (isset($per_page) ? $per_page : 10) ?>;
	var fixed_order = <?php echo (isset($fixed_order) ? $fixed_order : 0) ?>;
	var params = {
		apiKey: openpne.apiKey,
		cid: <?php echo $communityId ?>,
<?php if(isset($req_mid)) : ?>
		mid: <?php echo $req_mid ?>,
<?php endif; ?>
		count: per_page,
		fixed: fixed_order
	}

	function __updateComments(elem, topicId){
		$(".dsl_comments", elem).addClass("dslLoading");
		$.getJSON(openpne.apiBase + 'dsls/comments',{
				apiKey: openpne.apiKey,
				community_topic_id: topicId,
				count: 0
			},
			function(res){
				$(".dsl_comments", elem).html("(コメント" + res.data.length + "件)").removeClass("dslLoading");
				if(res.data.length > 0){
					var lastone = res.data[res.data.length - 1];
					var items = [lastone];
					var entry = $("#commentEntry").tmpl(items, {
						calcTimeAgo: function(){
							return moment(this.data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow();
						}
					});
					$("<div class='dsl_comment_list'></div>")
					 .appendTo($(".container_comments", elem))
					 .append(entry)
					 .find("div.dsl_comment:last")
					 .prepend('<a href="<?php echo public_path($path_open_entry)?>/' + topicId + '#comment=' + lastone.number + '">最新コメント</a>:')
					 .click(function(evt){
						window.location.href = "<?php echo public_path($path_open_entry)?>/" + topicId + "#comment=" + lastone.number;
						evt.stopPropagation();
					 });
				}
			}
		);
	}
	var listelem = $("#<?php echo $partId ?> .dsl_topiclist");
	function __render(res){
		var len = res.data.length;
		if (len > 0){
			var entry = $('#topicEntry').tmpl(res.data,{
				calcTimeAgo: function(){
					return moment(this.data.created_at, 'YYYY-MM-DD HH:mm:ss').fromNow();
				}
			});
			listelem.append(entry);
			entry.click(function(){
				window.location.href = "<?php echo public_path($path_open_entry)?>/" + $(this).attr("topic_id");
			});
			entry.each(function(){
				__updateComments($(this), $(this).attr("topic_id"));
			});
			var offs = 1 * (res.offs || 0);
			if(res.total >= (len + offs)){
				var topmenu = $("#<?php echo $partId ?> .dsl_topiclist_top_menu")
				 .empty().html("<li style='float:right;'><span><b>" + res.total + "</b>件中 <b>" + (offs + 1) + "</b>" + (len == 1 ?  "": "～<b>" + (offs + len) + "</b>") + "件目を表示 <input style='padding:2px;' type='button' class='input_submit move_back' value='前へ'/> <input style='padding:2px;' type='button' class='input_submit move_forward' value='次へ'/></span></li>");
				if(offs == 0){
					topmenu.find("input.move_back").attr("disabled", "true");
				}else{
					topmenu.find("input.move_back").click(function(){
						__set_offs_on_page(Math.max(offs - per_page, 0));
//						__update();
					});
				}
				if(res.total <= offs + len){
					topmenu.find("input.move_forward").attr("disabled", "true");
				}else{
					topmenu.find("input.move_forward").click(function(){
						__set_offs_on_page(Math.min(offs + per_page, res.total));
//						__update();
					});
				}
			}
		}
		else{
			listelem.append('<div class="dsl_topic dsl_empty">トピックがありません</div>')
		}
	}
	var __partId = "<?php echo $partId ?>";
	function __set_offs_on_page(offs){
		var ourl = $.parse_url(window.location.href);
		var otmp = {};
		otmp["offs_" + __partId] = offs;
		ourl.fragments = $.extend(ourl.fragments || {}, otmp);
		window.location.href = $.serialize_url(ourl);
	}
	function __get_offs_from_page(){
		var ourl = $.parse_url(window.location.href);
		return (ourl.fragments ? ourl.fragments["offs_" + __partId] : 0) || 0;
	}
	var __cur_offs = 0;
	function __update(options){
		var o_url = $.parse_url(window.location.href);
		var offs = (options ? options.offs : 0) || __get_offs_from_page();
		if(__cur_offs != offs || options){
			options = options || {};
			__cur_offs = options.offs = offs;
			listelem.css('min-height', listelem.height() + 'px');
			listelem.empty();
			listelem.addClass("dslLoading");
			$.getJSON(openpne.apiBase + 'dsls/topics',
				$.extend({},params,options),
				function(res)
				{
					listelem.removeClass("dslLoading");
					__render(res);
					listelem.css('min-height', '');
				}
		  	);
		}
	}
	$("#updateButton").click(__update);
	$(window).on("hashchange", function(e){
		__update();
	});
	__update({offs:0});
});
</script>
