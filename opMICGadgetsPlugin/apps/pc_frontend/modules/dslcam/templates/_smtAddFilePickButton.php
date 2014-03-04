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
<script type="text/javascript">
$(document).ready(function(){
	var __fmt_file_max = "<?php echo $fmt_file_max ?>";
	if(!$("head link[rel='stylesheet'][href*='opMICGadgetsPlugin/css/tosaka_override.css']").length ){
		$("<link rel='stylesheet' type='text/css' media='screen' href='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/css/tosaka_override.css" ?>'>").appendTo("head");
	}
	var MAXLENGTH = 140;
	var __errmsgs = {
		"file_size": ["ファイルサイズは", __fmt_file_max, "までです"].join(""),
		"upload": "アップロードに失敗しました",
		"not_image": "画像をアップロードしてください",
		"tweet": "投稿に失敗しました"
	};
	var __tweet_errmsgs = {
		"The body text is too long.": "文章が長すぎます(140文字まで)"
	};
	var error_area = $("<div/>", {"class": "row dsl_post_error"}).insertAfter($(".postform .row.posttextarea"));
	var submit_org = $('#tosaka_postform_submit');
	submit_org.clone(false).attr("id", "tosaka_postform_submit_ovrd").css("margin-top",".4em").insertAfter(submit_org).click(function(){
		var body_elm = $("#tosaka_postform_body");
		var body_txt = body_elm.val();
		if(!body_txt){
			return;
		}
		var tmp = $(this);
		tmp.attr("disabled", true);
		tmp.addClass("dsl_tosaka_loading");
		var img_elem = $("#tosaka_postform_img_captured");
		error_area.empty();
		$.post(openpne.apiBase + "activityext/postWithImage?apiKey=" + openpne.apiKey, {
			apiKey: openpne.apiKey,
			body: body_txt,
			dataType: "json",
			type: img_elem.attr("tmp_type") || "image/png",
			filename: img_elem.attr("tmp_filename") || "captured_tmp.png",
			data: img_elem.attr("src")
			
		}, function(data, status, xhr){
			tmp.attr("disabled", false);
			tmp.removeClass("dsl_tosaka_loading");
			if("string" == typeof(data)){
				data = $.parseJSON(data);
			}
			if("success" == status && data.status && "error" != data.status){
				body_elm.val("");
				$("#tosaka_postform_img_captured").remove();
				$(".postform").toggle();
				window.location.reload();
			}else{
				error_area.html(__tweet_errmsgs[data.message || ""] || __errmsgs[data.type]);
			}
		});
	});
	submit_org.hide();
	var counter = $(".postform .row #tosaka_postform_counter");
	if(0 == counter.length){
		counter = $("<span>", {
			"class": "dsl_counter"
		}).insertBefore(".postform .row #tosaka_postform_submit")
		.html("" + MAXLENGTH);
		$(".postform #tosaka_postform_body").on("keyup change", function(){
			var left = MAXLENGTH - $(this).val().length;
			if(0 > left){
				counter.addClass("exceed");
			}else{
				counter.removeClass("exceed");
			}
			counter.html("" + left);
		});
	}
	var filepick = $(".postform .row #tosaka_postform_filepick");
	if(0 == filepick.length){
		$("<div class='file_btn_container'>写真を添付（"+ __fmt_file_max + "まで）<input type='file' accept='image/*' class='filepick'/></div>")
		 .insertBefore(".postform .row #tosaka_postform_submit")
		 .find(".filepick")
		 .change(function(){
			var filepick = $(this);
			var r = new FileReader();
			var file = this.files[0];
			r.onload = function(evt){
				var img = $("#tosaka_postform_img_captured");
				if(0 == img.length){
					img = $("<img id='tosaka_postform_img_captured' style='height:5em;'/>").insertAfter(filepick.parent());
				}
				img.attr("tmp_type", file.type);
				img.attr("tmp_filename", file.name);
				img.attr("src", r.result);
			};
			r.readAsDataURL(file);
		});
	}
});
</script>
