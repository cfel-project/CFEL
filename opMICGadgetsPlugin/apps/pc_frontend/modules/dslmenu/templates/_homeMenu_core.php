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
	var __menu_items = <?php echo htmlspecialchars_decode($menuJson) ?>;
	var __menu_cols = 4;

	var __id = "home_menu_<?php echo $gadget->id ?>";

	function __getByClass(cls){
		return $("#" + __id + " ." + cls);
	}
	var __conv = {
		"community": function(itm){
			return {
				title: itm.src.title,
				image: itm.src.image,
				href: itm.src.url
			};
		},
		"url": function(itm){
			return {
				title: itm.title,
				href: itm.url || "javascript:" + (itm.action || "")
			};
		},
		"path": function(itm){
			return {
				title: itm.title,
				href: openpne.apiBase.replace(/api\.php\/$/, "")
			};
		}
	};
	function __render(items){
		var e_width = __getByClass("hm_menu_container").width() / __menu_cols - 9;
		var menu_entries = $.map(items, function(val,i){
			return $.extend({},{
					width:e_width,
					height: .8 * e_width,
					fntsz: .1 * e_width,
					imgheight: .3 * e_width,
					cls:"hm_menu_" + val.style
				},val);
		});
		__getByClass("hm_menu_container")
		 .append(
			$("#hm_menu_entry_tmpl").tmpl(menu_entries)
		 );
	}
	__render(__menu_items.items);
});
</script>
<script id="hm_menu_entry_tmpl" type="text/x-jquery-tmpl">
	<a class="hm_menu_entry ${cls}" href="${href}" style="width:${width}px;height:${height}px;margin:.2em;font-size:${fntsz}px;line-height:${fntsz}px;">
		<div style="margin:1em .5em;">
			{{if image}}<div style="vertical-align:middle;">
				<img src="${image}" style="height:${imgheight}px;border-radius:45%;"/>
			</div>{{/if}}
			<div style="vertical-align:middle;line-height:1.1em;">{{html html}}</div>
		</div>
	</a>
</script>
