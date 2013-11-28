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
use_helper("Javascript");
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
?>
<script id="form_event_category_option_tmpl" type="text/x-jquery-tmpl">
	<option value="${name}">${title}</option>
</script>
<script type="text/javascript">
$(document).ready(function(){
	var __support_date_input = ("date" == $("<input type='date'/>")[0].type);
	var __support_time_input = ("time" == $("<input type='time'/>")[0].type);

	function overrideDateField(src, trg, olistener){
		src.appendTo(trg.parent().wrapInner("<span style='display:none;'/>"))
		.change(function(){
			var dtelems = $(this).val().split("-");
			olistener["year"].val(1 * dtelems[0]);
			olistener["month"].val(1 * dtelems[1]);
			olistener["day"].val(1 * dtelems[2]);
		});
		var oy = olistener["year"].val();
		var om = olistener["month"].val();
		var od = olistener["day"].val();
		if(oy && om && od){
			src.val(moment([oy, om, od].join("-"), "YYYY-MM-DD").format("YYYY-MM-DD"));
		}
	}
	if(__support_date_input && 0 == $("#formCommunityEvent #op_date_input").length){
		overrideDateField(
			$("<input id='op_date_input' type='date'/>"),
			$("#formCommunityEvent form [name^='community_event[open_date]']"),
			{
				"year":$("#formCommunityEvent form [name='community_event[open_date][year]']"),
				"month":$("#formCommunityEvent form [name='community_event[open_date][month]']"),
				"day":$("#formCommunityEvent form [name='community_event[open_date][day]']")
		});
	}

	if(__support_date_input && 0 == $("#formCommunityEvent #dl_date_input").length){
		overrideDateField(
			$("<input id='dl_date_input' type='date'/>"),
			$("#formCommunityEvent form [name^='community_event[application_deadline]']"),
			{"year": $("#formCommunityEvent form [name='community_event[application_deadline][year]']"),
				"month": $("#formCommunityEvent form [name='community_event[application_deadline][month]']"),
				"day": $("#formCommunityEvent form [name='community_event[application_deadline][day]']")
			}
		);
	}
	if(0 == $("#formCommunityEvent #time_container").length){
		var _f_date_comm = $("#formCommunityEvent form [name='community_event[open_date_comment]']");
		var _time_container = $("<span id='time_container'><input style='width:6em;' type='time' id='time_start'/> - <input style='width:6em;' type='time' id='time_end'/></span>").appendTo(_f_date_comm.parent().wrapInner("<span style='display:none;'/>"));
		_time_container.find("#time_start").val(_f_date_comm.val().split("-")[0])
		 .click(function(){
			_f_date_comm.val([$(this).val(), _f_date_comm.val().split("-")[1] || ""].join("-"));
		})
		 .change(function(){
			_f_date_comm.val([$(this).val(), _f_date_comm.val().split("-")[1] || ""].join("-"))
		});
		_time_container.find("#time_end").val(_f_date_comm.val().split("-")[1])
		 .click(function(){
			_f_date_comm.val([_f_date_comm.val().split("-")[0], $(this).val()].join("-"));
		})
		 .change(function(){
			_f_date_comm.val([_f_date_comm.val().split("-")[0], $(this).val()].join("-"));
		});
		
	}

	var __options = <?php echo htmlspecialchars_decode(!empty($optionsjson) ? $optionsjson : "null"); ?>;

	if(0 == $("#formCommunityEvent #category_select").length && __options){
		var sel = $("<select id='category_select'/>")
		 .appendTo(
			$("#formCommunityEvent form [name='community_event[area]']")
			 .parent()
			 .wrapInner("<span style='display:none;'/>")
		).append($("#form_event_category_option_tmpl").tmpl(__options))
		.change(function(){
			$("#formCommunityEvent form [name='community_event[area]']").val($(this).val());
		});
		var orgval = $("#community_event_area").val();
		if(orgval){
			sel.val(orgval);
		}else{
			$("#community_event_area").val(sel.val());
		}
	}
	$("#formCommunityEvent label[for='community_event_area']").html("予約区分 <strong>*</strong>");

});
</script>
