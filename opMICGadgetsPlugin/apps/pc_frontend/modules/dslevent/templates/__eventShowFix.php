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
<?php if(isset($sendParam)) :
use_javascript("/opMICGadgetsPlugin/js/jq.event_fix.js", "last");
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("#linkLine a").each(function(){
		$(this).attr("href", $(this).attr("href").replace(/\/community\//, "/dslevent/listCommunity/")<?php echo (!empty($communityEvent) ? " + '#eventlist__strt=".strtotime($communityEvent[open_date])."000'" : "")?> );
		$(this).html("イベント一覧");
	});

	var __fix_design = function(){
		$("#communityEvent .parts table tbody tr:eq(3) th").text("予約区分");
	};
	var __editForm = $("#Center .operation form");
	var __set_mail_message = function(msg){
		var button = $("ul.button.moreInfo", __editForm);
		button.siblings("div.mailMessage").remove();
		button.after([
			"<div class='mailMessage'>",
			msg,
			"</div>"].join(""));
	};

	$("<li><input id='send_mail_button' class='input_submit' type='submit' value='イベントの最新情報を全員にメールする'></li>").appendTo("#Center>.operation ul.button.moreInfo")
	 .find("#send_mail_button")
	 .dsl_event_notify({data:{
		"eid":"<?php echo $sendParam['eventId']?>",
		"cid":"<?php echo $sendParam['commId']?>"
		},
		success: function(data){
			__set_mail_message("メールが送信されました");
		},
		error: function(msg, err){
			__set_mail_message(["メール送信に失敗しました：", msg || "", err || ""].join(" "));
		}
	});
	var __fnode = $("form[action$='/comment/create']");
	var __post_joincancel = function(src){
		src.attr("disabled", "true");
		src.addClass("loading");
		$.post([openpne.apiBase, "event/join.json"].join(""),
			{
				apiKey: openpne.apiKey,
				id: __fnode.attr("action").replace(/\/comment\/create$/,"").replace(/.*\//g, ""),
				leave: ("cancel" == src.attr("name") ? true : undefined)
			},
			"json")
		 .success(function(res){
			window.location.reload();
		})
		 .error(function(res){
			src.removeAttr("disabled");
			src.removeClass("loading");
			alert(res);
		});
	};
	__fnode.find("input[name='participate']").click(function(){
		if(!__fnode.find("#community_event_comment_body").val()){
			__post_joincancel($(this));
		}else{
			$(this).attr("disabled", "true");
			$(this).addClass("loading");
			__fnode.submit();
		}
	});
	__fnode.find("input[name='cancel']").click(function(){
		if(!__fnode.find("#community_event_comment_body").val()){
			__post_joincancel($(this));
		}else{
			$(this).attr("disabled", "true");
			$(this).addClass("loading");
			__fnode.submit();
		}
	});
	
	__fix_design();
});
//]]>
</script>
<?php endif; ?>