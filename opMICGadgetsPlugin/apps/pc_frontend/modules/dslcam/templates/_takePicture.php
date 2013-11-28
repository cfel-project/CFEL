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
	var __id = "videocap_<?php echo $gadget->id ?>";

	function __getByClass(cls){
		return $("#" + __id + " ." + cls);
	}
	function __disablevideo(){
		__getByClass("video_container").hide();
		__getByClass("video_unavailable").show();
	}

	navigator.__getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
	if(navigator.__getUserMedia){
		navigator.__getUserMedia({video:true},
			function(lms){
				var mv = __getByClass("camoutput")[0];
				mv.src = window.URL.createObjectURL(lms);
				__getByClass("takepic").click(function(){
					var ht = mv.videoHeight;
					var wt = mv.videoWidth;
					var cnv = __getByClass("tmpframe");
					cnv.attr("height", ht);
					cnv.attr("width", wt);
					cnv[0].getContext("2d").drawImage(mv,0,0,wt,ht,0,0,wt,ht);
					var img = new Image();
					img.src = cnv[0].toDataURL("image/png");
					img.onload = function(){
						window.open(img.src, "_blank");
					}
				});
				mv.play();
			},
			function(err){
				__disablevideo();
			}
		);
	}else{
		__disablevideo();
	}
});
</script>
<div id="videocap_<?php echo $gadget->id ?>">
	<div class="video_container">
		<input type="button" value="保存" class="takepic"/>
		<video class="camoutput" style="width:100%;">↑"カメラを使用する"に許可をお願いします。</video>
		<canvas class="tmpframe" style="display:none;"></canvas>
	</div>
	<div class="video_unavailable" style="display:none;">
		Web Cameraを接続し、Chromeで使用してください
	</div>
</div>
