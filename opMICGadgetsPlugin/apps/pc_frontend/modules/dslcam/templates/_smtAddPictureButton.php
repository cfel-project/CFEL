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
	var submit_org = $('#tosaka_postform_submit');
	submit_org.clone(false).attr("id", "tosaka_postform_submit_ovrd").insertAfter(submit_org).click(function(){
		var body_elm = $("#tosaka_postform_body");
		var body_txt = body_elm.val();
		if(!body_txt){
			return;
		}
		var tmp = $(this);
		tmp.attr("disabled", true);
		tmp.addClass("dsl_tosaka_loading");
		$.post(openpne.apiBase + "activityext/postWithImage?apiKey=" + openpne.apiKey, {
			apiKey: openpne.apiKey,
			body: body_txt,
			type: "image/png",
			filename: "captured_tmp.png",
			data: $("#tosaka_postform_img_captured").attr("src")
			
		}, function(data, status, xhr){
			tmp.attr("disabled", false);
			tmp.removeClass("dsl_tosaka_loading");
			if("success" == status){
				body_elm.val("");
				$(".postform").toggle();
			}
		});
	});
	submit_org.hide();
	
	var toggle_cam = $(".postform .row #tosaka_postform_togglecam");
	navigator.__getUserMedia = navigator.__getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;

	if(0 == toggle_cam.length){
		toggle_cam = $("<button id='tosaka_postform_cambutton' class='btn'><i class='icon-camera'></i></button>")
		 .insertBefore(".postform .row #tosaka_postform_submit")
		 .click(function(){
			if(navigator.__getUserMedia){
				navigator.__getUserMedia({video:true},
					function(lms){
						var camvideo = $(".postform .row #tosaka_postform_camvideo");
						if(0 == camvideo.length){
							camvideo = $("<video autoplay class='camoutput' style='width:100%;'>カメラを使用するに許可をお願いします。</video>").insertBefore(toggle_cam);
						}
						var mv = camvideo[0];
						toggle_cam.hide();
						mv.src = window.URL.createObjectURL(lms);
						var takepic = $(".postform .row #tosaka_postform_takepic");
						if(0 == takepic.length){
							takepic = $("<button class='btn'><i class='icon-camera'></i></button>").insertBefore(toggle_cam)
								 .click(function(){
									var ht = mv.videoHeight;
									var wt = mv.videoWidth;
									var cnv = $("#vc_tosaka_tmpframe_<?php echo $gadget->id ?>");
									cnv.attr("height", ht);
									cnv.attr("width", wt);
									cnv[0].getContext("2d").drawImage(mv,0,0,wt,ht,0,0,wt,ht);
									var img = $("#tosaka_postform_img_captured");
									if(0 == img.length){
										img = $("<img id='tosaka_postform_img_captured' style='height:5em;'/>").insertBefore(camvideo);
									}
									img.attr("src", cnv[0].toDataURL("image/png"));
								});
						}
						mv.play();
					}, function(err){
						toggle_cam.attr("disabled", true);
					}
				);
			}
		});
	}
});

</script>
<canvas id="vc_tosaka_tmpframe_<?php echo $gadget->id ?>" style="display:none;"></canvas>
