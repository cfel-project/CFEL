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
<style type="text/css">
.row .gadget_header{
	height:auto;
	background: transparent;
	color:#3f312b;
	text-align:inherit;
	font-weight:bold;
	width:auto;
	border-left: double #eb6238 .5em;
	margin-left:.1em;
	padding-left: .2em;
}

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
	border-width: 0 .5em .5em 0;
	border-style: solid;
	display: block;
	width: 0;
	bottom: -.5em;
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

.speechBubble.button{
	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.6, #fffffc), color-stop(1, #d4dcda));
	background:-moz-linear-gradient( center top, #fffffc 60%, #d4dcda 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#fffffc', endColorstr='#d4dcda', endColorstr=#fffffc');
	background-color:#fffffc;
}

.speechBubble.button:after{
	border-color: transparent #d4dcda;
}


</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".navbar img.postbutton")
	 .before(
		$("<span style='margin:0 .5em;font-weight:bold;line-height:2em;'>つぶやく</span>")
	)
	 .css({
		"position":"absolute",
		"left": 0,
		"width":"100%",
		"height":"100%"
	})
	 .attr("src", "<?php echo url_for('opMICGadgetsPlugin/images')?>/transparent.png")
	 .parent()
	 .addClass("speechBubble button");
//hide switch to pc mode 
	$(".navbar .nav a#smt-switch").parent().hide();

//override communityTopic creation page //we might better to make new page for show event and use this code there..(but may costs a lot)
	(function(){
		var isCommunityTopicNew = function(){
			return ("" + window.location.href).match(/\/communityTopic\/new\//);
		};
		if(isCommunityTopicNew() && window["toggleSubmitState"] && !window["__toggleSubmitState_org"]){
			window["__toggleSubmitState_org"] = toggleSubmitState;
			toggleSubmitState = function(){
				var showtopic = $("#successMessage>a");
				if(0 <showtopic.length){
					window.location.href = showtopic.attr("href");
				}else{
					__toggleSubmitState_org();
				}
			};
		}
	})();
//override communityTopic page //we might better to make new page for show event and use this code there..(but may costs a lot)
	(function(){
		var isCommunityTopicPage = function (){
			var paths = ("" + window.location.href).split("/");
			return ("communityTopic" == paths[paths.length - 2]);
		};
		if(isCommunityTopicPage()){
			$("body>ul.footer>li:nth-child(2)").hide();
			var link_list = $("body>ul.footer>li:nth-child(1)>a");
			if(0 < link_list.length){
				link_list.attr("href", link_list.attr("href").replace(/\/communityTopic\//,"/d_topic/"));
			}
			//fix:the url after removal is not correct.
			var evobj = $("#deleteEntryModal .modal-button#execute").data("events").click;
			if(evobj.length > 0){
				evobj[0].handler = function(e){
					if("execute" == e.target.id){
						$.post(openpne.apiBase + "topic/delete.json",
						{apiKey: openpne.apiKey, id: topic_id},
						"json")
						 .success(function(res){
							window.location = "<?php echo url_for("d_topic/listCommunity")?>/" + res.data.community_id;//change first path for main stream.
						})
						 .error(function(res){
							if(window["console"]){
								console.log(res);
							}
						});
					}else{
						$("#deleteEntryModal").modal("hide");
					}
				};
			}
		}
	})();

//override showEvent page//we might better to make new page for show event and use this code there..(but may costs a lot)
	(function(){
		var isShowEventPage = function (){
			var paths = ("" + window.location.href).split("/");
			return ("communityEvent" == paths[paths.length - 2]);
		};
		if(isShowEventPage()){
			function overridePostHandler(selector,def_msg){
				var evobj = $($(document).data("events").click).filter(function(){return selector == this.selector;});
				if(evobj.length > 0){
					var orgh = evobj[0].handler;
					evobj[0].handler = function(){
						if (0 >= $.trim($('input#commentBody').val()).length){
							$('input#commentBody').val(def_msg);
						}
						orgh();
					};
				}
			}
			overridePostHandler("#postJoin", "参加します");
			overridePostHandler("#postCancel", "取りやめます");
			 $("body>ul.footer>li:nth-child(2)").hide();
			var link_list = $("body>ul.footer>li:nth-child(1)>a");
			if(0 < link_list.length){
				link_list.attr("href", link_list.attr("href").replace(/\/communityEvent\//,"/dslevent/"));
				var c_intv = setInterval(function(){
					var f_opdt = $("#show>div.row:nth-child(5)>div.span9");
					if(0 < f_opdt.length && f_opdt.text()){
						clearInterval(c_intv);
						var open_date = moment(f_opdt.text()
						 .replace(/\D/g,"/").replace(/\/$/,"")).toDate();
						link_list.attr("href",link_list.attr("href") + "#eventlist__strt=" + open_date.getTime());
					}
				}, 500);
			}
		}
	})();
	
});
</script>
