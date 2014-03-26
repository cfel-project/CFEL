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
<script id="mic_ext_member_list_tmpl" type="text/x-jquery-tmpl">
<div class="member_entry_container sheet_float" style="position:relative;">
	<div class="member_entry_header">
		<a href="${profile_url}"><img class="profile" src="${profile_image}"/><div class="screen-name" style="text-align:center;">${screen_name}</div></a>
	</div>
	<div class="member_entry_detail">
		{{if self_introduction}}
		<div class="member_speechbubble">
			<a href="${profile_url}"><div class="screen-name">${screen_name}</div></a>
			<div class="introduction">${self_introduction}</div>
		</div>
		{{else}}
			<div class="introduction nocontents">まだ自己紹介が書かれていません</div>
		{{/if}}
		<div class="stats_container">
			{{if last_cnfrm}}
			<div class="confirmation_stats">${$item.showFromnow()} に 
				{{if last_cnfrm.value == "1"}}
				<span class="stat_fine">元気</span>
				{{else}}
				<span class="stat_bad">調子が悪い</span>
				{{/if}}
				と回答
			</div>
			{{else}}
			<div class="confirmation_stats nocontents">まだ安否確認の回答がありません</div>
			{{/if}}
			<div class="last_login">${$item.showLastLogin()}にログイン</div>
		</div>
	</div>
<div style="clear:both;"></div>
</div>
</script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	var __id = "ping_member_list_<?php echo $gadget->id ?>";
	var __last_data = null;
	var __sort_map = {
		"last_login": function(a,b){
			return a.last_login - b.last_login;
		},
		"last_confirm": function(a,b){
			return (a.last_cnfrm ? a.last_cnfrm.time : 0) - (b.last_cnfrm ? b.last_cnfrm.time : 0);
		},
		"last_confirm_val": function(a,b){
			return (a.last_cnfrm ? 1 * a.last_cnfrm.value : 0) - (b.last_cnfrm ? 1 * b.last_cnfrm.value : 0);
		}
	};
	for (var k in __sort_map){
		(function(key){
			var tmp = __sort_map[key];
			__sort_map[key + "_desc"] = function(a,b){
				return -1 * tmp(a,b);
			};
		})(k);
	}
	var opt_elem = $("#" + __id + " select.sort_option").change(function(){
		__update();
	});
	var __update = function(data){
		__last_data = data || __last_data;
		__last_data.sort(__sort_map[opt_elem.val()]);
		$("#" + __id + " .cnfrm_member_list_container")
		 .empty()
		 .html($("#mic_ext_member_list_tmpl").tmpl(__last_data, {
			showFromnow: function(){
				return moment(1000 * this.data.last_cnfrm.time).fromNow();
			},
			showLastLogin: function(){
				return moment(1000 * this.data.last_login).fromNow();
			}
		}));
	};
	$.getJSON(openpne.apiBase + "memberext/search",{
		apiKey: openpne.apiKey
	})
	 .success(function(json){
		opt_elem.removeAttr("disabled");
		__update(json.data);
	})
	 .error(function(xhr, message, error){
		$("#" + __id + " .cnfrm_member_list_container").empty().html("エラー: "+message + " - " + error);
	});

});
//]]>
</script>