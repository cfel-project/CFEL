<?php
/*******************************************************************************
 * Copyright (c) 2011, 2013 IBM Corporation and Others
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
	var submit_org = $('#tosaka_postform_submit');
	submit_org.clone(false).attr("id", "tosaka_postform_submit_ovrd").insertAfter(submit_org).click(function(){
		var body_elm = $("#tosaka_postform_body");
		var body_txt = body_elm.val();
		if(!body_txt){
			return;
		}
		var img_elem = $("#tosaka_postform_img_captured");
		$.post(openpne.apiBase + "activityext/postWithImage?apiKey=" + openpne.apiKey, {
			apiKey: openpne.apiKey,
			body: body_txt,
			type: img_elem.attr("tmp_type") || "image/png",
			filename: img_elem.attr("tmp_filename") || "captured_tmp.png",
			data: img_elem.attr("src")
			
		}, function(data, status, xhr){
			if("success" == status){
				body_elm.val("");
				$("#tosaka_postform_img_captured").remove();
				$(".postform").toggle();
				window.location.reload();
			}
		});
	});
	submit_org.hide();
	var filepick = $(".postform .row #tosaka_postform_filepick");
	if(0 == filepick.length){
		filepick = $("<input type='file' accept='image/*' class='filepick'/>")
		 .insertBefore(".postform .row #tosaka_postform_submit")
		 .change(function(){
			var r = new FileReader();
			var file = this.files[0];
			r.onload = function(evt){
				var img = $("#tosaka_postform_img_captured");
				if(0 == img.length){
					img = $("<img id='tosaka_postform_img_captured' style='height:5em;'/>").insertAfter(filepick);
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
