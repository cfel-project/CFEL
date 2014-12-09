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
<style type="text/css">
.row .gadget_header{
	height:auto;
	background: transparent;
	color:#3f312b;
	text-align:inherit;
	font-weight:bold;
	width:auto;
	border-left: double #eb6238 .5em;
	margin:.2em .1em 0;
	padding-left: .2em;
}

.messageBox,
.speechBubble{
	position: relative;
	margin: .1em .2em;
	border: solid .1em #698aab;
	-webkit-border-radius: .7em;
	-moz-border-radius: .7em;
	border-radius: .7em;
	-webkit-box-shadow: 1px 2px 2px rgba(0, 0, 0, 0.2);
	-moz-box-shadow: 1px 2px 2px rgba(0, 0, 0, 0.2);
	box-shadow: 1px 2px 2px rgba(0, 0, 0, 0.2);
	background: #fffffc;
	color: #494a41;
}

.speechBubble:before{
	content: "";
	position: absolute;
	right: 13px;
	border-width: 0 .55em .55em 0;
	border-style: solid;
	display: block;
	width: 0;
	bottom: -.55em;
	left: auto;
	border-color: transparent #698aab;
}
.speechBubble:after{
	content: "";
	position: absolute;
	right: 14px;
	border-width: 0 .5em .5em 0;
	border-style: solid;
	display: block;
	width: 0;
	bottom: -.4em;
	left: auto;
	border-color: transparent #fffffc;
}

.speechBubble.utd:before{
	border-width: 0 0 .5em .5em;
	bottom: auto;
	top: -.5em;
	border-color: #698aab transparent;
}

.speechBubble.utd:after{
	border-width: 0 0 .5em .5em;
	bottom: auto;
	right: 14px;
	top: -.3em;
	border-color: #fffffc transparent;
}

.speechBubble.button{
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.6, #fffffc), color-stop(1, #d4dcda));
	background:-moz-linear-gradient( center top, #fffffc 60%, #d4dcda 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#fffffc', endColorstr='#d4dcda', endColorstr=#fffffc');
	background-color:#fffffc;
}

.speechBubble.button:after{
	border-color: transparent #d4dcda;
}

.messageBox.dark{
	background-color: #1e50a2;
	color: #fffffc;
}

/*hide read  notifications*/
#pushList .isread{
	display:none;
}

/*
	fix for css3 transition set in original code is not working:
	collapse need to be set on the element from the beginning (like toggle1)
	, and "in" class should be applied when they want to make it "expand".
	also, height:auto for transition-property does not work as expected
	, use max-height instead.
	http://stackoverflow.com/questions/3508605/css-transition-height-0-to-height-auto
	they seems to set "toggle1" class for the elements that need to be set "collapse", hence I'm going to use that class instead for this version.
	remove this section when the original code become work as expected..
*/

body .collapse {
	-webkit-transition: max-height 0.35s ease;
	-moz-transition: max-height 0.35s ease;
	-ms-transition: max-height 0.35s ease;
	-o-transition: max-height 0.35s ease;
	transition: max-height 0.35s ease;
	height:auto;
}

body .collapse{
	max-height:0;
}
/*collapse seems to be used as "in" ... */
body .collapse.in{
	max-height:100em;
}

</style>
<script type="text/javascript">
$(document).ready(function(){
	if(!$("head link[rel='stylesheet'][href*='opMICGadgetsPlugin/css/gadgets.css']").length ){
		$("<link rel='stylesheet' type='text/css' media='screen' href='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/css/gadgets.css" ?>'>").appendTo("head");
	}
	var post_ovr_text = 
	$(".navbar img.postbutton")
	 .before(
		$("<span class='postbutton_ovr_text' style='margin:0 .5em;font-weight:bold;line-height:2em;'>つぶやく</span>")
	)
	 .css({
		"position":"absolute",
		"left": 0,
		"width":"100%",
		"height":"100%"
	})
	 .attr("src", "<?php echo url_for('opMICGadgetsPlugin/images')?>/transparent.png")
	 .parent()
	 .addClass("speechBubble button")
	 .find(".postbutton_ovr_text");

//override postform, ncform transition//
	var toggles = $(".toggle1").addClass("collapse");
	$(".toggle1_close").off("click").on("click",function(){
		toggles.removeClass("in");
	});;
	var postform = $(".postform").removeClass("hide").css({padding:0,"border-bottom":0});
	var ncform = $(".ncform").removeClass("hide").css({padding:0,"border-bottom":0});;
	$.each([postform.find(".row:last-child"), ncform.find("#pushList")], function(){
		this.css({"padding-bottom":"1em","border-bottom":"6px solid #909090"});
	});
	$(".ncbutton").off("click").on("click", function(){
		if(ncform.hasClass("in")){
			ncform.removeClass("in");
		}else{
			$.getJSON( openpne.apiBase + "push/search.json",{
				apiKey:openpne.apiKey
			}, function(res){
				$("#pushLoading").hide();
				if("success" == res.status){
					var nodes = $("#pushListTemplate").tmpl(res.data);
					$(".friend-accept", nodes).friendLink({
						buttonElement:".friend-notify-button",
						ncfriendloadingElement:"#ncfriendloading",
						ncfriendresultmessageElement:"#ncfriendresultmessage"
					});
					$(".friend-reject", nodes).friendUnlink({
						buttonElement:".friend-notify-button",
						ncfriendloadingElement:"#ncfriendloading",
						ncfriendresultmessageElement:"#ncfriendresultmessage"
					});
					$("#pushList").html(nodes).show();
				}else{
					alert(res.message);
				}
				$(".nclink").pushLink();
			});
			$(".toggle1:not(.ncform)").removeClass("in");
			ncform.addClass("in");
		}
	});
	$(".btn-navbar").off("click").on("click", function(){
		if($(".toggle1.nav-collapse").hasClass("in")){
			$(".toggle1.nav-collapse").removeClass("in");
		}else{
			$(".toggle1:not(.nav-collapse)").removeClass("in");
			$(".toggle1.nav-collapse").addClass("in");
		}
	});
	$(".postbutton").off("click").on("click", function(){
		if(postform.hasClass("in")){
			postform.removeClass("in");
			post_ovr_text.html("つぶやく");
		}else{
			$(".toggle1:not(.postform)").removeClass("in");
			postform.addClass("in");
			post_ovr_text.html("閉じる");
		}
	});

//end override postform, ncform transition//

//hide switch to pc mode 
	$(".navbar .nav a#smt-switch").parent().hide();
});
</script>
