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
<div class="modal hide" role="dialog" id="ping_box">
	<div class='modal-body mic_ping_user_container'>
		<div class="mic_ping_content">
			<div class="modal-header"><b><span class="greeting"></span> <?php echo $member["name"]?>さん</b></div>
			<div class="modal-body">調子はどうですか?</div>
			<div class="modal-footer" style="padding: .5em 1em;">
				<button class="btn btn-danger mic_ping_user_sick">あまりよくありません</button>
				<button class="btn btn-primary mic_ping_user_healthy">元気です</button>
			</div>
		</div>
		<div style="display:none;" class="mic_ping_message">
		</div>
	</div>
</div>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __start = function(){
		var hours = moment().hours();
		$("#ping_box .greeting").html((4 < hours && 12 > hours) ? "お早うございます" : (18 <= hours || 4 >= hours) ? "こんばんは" : "こんにちは");
		$("#ping_box").on("shown", function(){
			$(this).css({"top": (($(".modal-backdrop").height() - $(this).height()) / 2) + "px", "position": "fixed"});
	//		var w_height = Math.max(window.outerHeight, $(window).height());
	//		$(".modal_backdrop").css({"top": 0, "height": w_height});
	//		var v_height = window.innerHeight || $(window).height();
	//		$(this).css("top", window.scrollY + ((v_height - $(this).height()) / 2));
		}).modal("show");//({backdrop:"static", keyboard:false, show: true} );

		function __send_value(val, message){
			$("#ping_box .mic_ping_content").fadeOut(100, function(){
				$("#ping_box .mic_ping_message").html(message).fadeIn(400, function(){
					var tm = (new Date()).getTime();
					$.post(openpne.apiBase + "ping/post",{
						apiKey: openpne.apiKey,
						value: val
					}, "json")
					 .success(function(res){
						setTimeout(function(){
							$("#ping_box").modal("hide");
						}, tm + 3000 - (new Date()).getTime());
					})
					 .error(function(res){
						setTimeout(function(){
							$("#ping_box").modal("hide");
						}, tm + 3000 - (new Date()).getTime());
					});
				});
			});
		}
		$("#ping_box button.mic_ping_user_healthy").click(function(){
			
			__send_value(1, "今日も一日楽しみましょう！");
		});
		$("#ping_box button.mic_ping_user_sick").click(function(){
			__send_value(-1, "お大事に。。");
		});
	};
	$.getJSON(openpne.apiBase + "ping/my",{
		apiKey: openpne.apiKey,
		limit: 1
	})
	.success(function(json){
		if(!json.num || !json.data || !json.data[0] || 0 < moment().diff(moment(1000 * json.data[0]["time"]), "days")){
			__start();
		}
	});

});
//]]>
</script>
