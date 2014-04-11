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
<?php if(isset($sendParam)) :?>
<script type="text/javascript">
(function(){
$(document).ready(function(){
	var __sendMessage = function(params){
		$.ajax({
			type:"POST",
			url:openpne.apiBase + 'dsls/reportTopic?apiKey=' + openpne.apiKey,
			data:{'tid':'<?php echo $sendParam["topicId"]?>',
			 'cid':'<?php echo $sendParam["commId"]?>',
			 'msg':params.msg || '<?php echo $sendParam["message"]?>'
			},
			success: params.success,
			error: params.error,
			dataType: "json"
		});
	};
	var __addButton = function(){//will not be used for this version.
		$("button.input_submit", $("#dsl_communitytopic_sender_<?php echo $gadget->id ?>")).click(function(){
			var node = $(this);
			node.addClass("dslLoading");

			__sendMessage({
				success: function(data){
					node.removeClass("dslLoading");
					if(data.status){
						alert(data.to + "に送られました");
					}
				},
				error: function(xhr, stText, err){
					node.removeClass("dslLoading");
				}
			});
		});
	};
	var __commentForm = $("#formCommunityTopicComment .parts form");

	var ftext = $("textarea[name='community_topic_comment[body]']",__commentForm);
	var __set_error_message = function(msg){
		ftext.siblings("div.error").remove();
		ftext.before([
			"<div class='error'><ul class='error_list'><li>",
			msg,
			"</li></ul></div>"].join(""));
	};
	var __inject_elems = function(){
		$(".operation ul.button.moreInfo", __commentForm)
		 .prepend("<li><label class='menu_option hide_until_ebis_send_ok'><?php echo isset($sendParam["caption"]) ? $sendParam["caption"] : 'このトピックを報告する' ?><input class='dsl_form_sendrequest_check' style='display:inline;float:left;' type='checkbox'></label></li>");
		__commentForm.submit(function(evt){
			evt.preventDefault();
			var node = $(this).find("input.input_submit");
			var text = (ftext.attr("value") || "").replace(/^\s+/,"").replace(/\s+$/,"");
			if(!text){
				__set_error_message("必須項目です");
			}else{
				node.addClass("dslLoading");
				node.attr("disabled", true)
				$.ajax({
					type:__commentForm.attr("method"),
					url:__commentForm.attr("action"),
					data:__commentForm.serialize(),
					success: function(data){
						if(__commentForm.find("input.dsl_form_sendrequest_check").attr("checked")){
							__sendMessage({
								msg:text,
								success: function(data){
									__set_error_message("メールが送信されました");
									window.location.reload();
								},
								error: function(xhr, stText, err){
									node.attr("disabled", false)
									node.removeClass("dslLoading");
									__set_error_message("コメントは追加されましたが、メール送信に失敗しました：".err);
								},
							});
						}else{
							window.location.reload();
						}
					},
					error: function(xhr, stText, err){
						node.attr("disabled", false)
						node.removeClass("dslLoading");
						__set_error_message("コメント追加に失敗しました：".err);
					}
				});
			}
		});
	};
	__inject_elems();
});
})();
</script>
<script type="text/javascript" src="<?php echo $ebisUrl ?>/ebis/report/verifyUserRole_script.jsp?vr=snd&dsl.preventcache=<?php echo time()?>&atkn=<?php echo $ebtoken ?>&aid=<?php echo $member->id?>$pnedsl"></script>
<!--<div id="dsl_communitytopic_sender_<?php echo $gadget->id ?>" class="dsl_communitytopic_sender_gadget parts line">
	<button style="border-radius:3px;" class="input_submit"><?php echo isset($sendParam["caption"]) ? $sendParam["caption"] : 'このトピックを報告する' ?></button>
</div> -->
<?php endif; ?>