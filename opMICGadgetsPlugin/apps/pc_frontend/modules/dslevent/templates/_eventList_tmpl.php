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
<script id="el_date_cell_templ" type="text/x-jquery-tmpl">
<?php if($hideNoEventDay){?>
{{if events.length}}
<?php }?>
	<div class="date_cell dsl_block ${optcls}"><div class="date_indicator">${datestr}</div>
		{{if events.length}}
			{{tmpl($item.data.events) "#el_event_elem_templ"}}
		{{else}}
			<div class="row entry no_event">この日の予定はありません</div>
		{{/if}}
		</div>
<?php if($hideNoEventDay){?>
{{/if}}
<?php }?>

</script>
<script id="el_event_elem_templ" type="text/x-jquery-tmpl">
	<div class="dsl_clickable row entry" link_href="${ev_url}">
		<span class="op_comment">${open_date_comment}</span>
		<span class="ev_title">
			<a href="${ev_url}">${name}</a>
			(${community_name})
		</span>
		<span class="ev_content">
			<span class="event-comment-list">${body_sum}</span>
			<div class="ev_status"><div class="event_elem{{if is_event_member}} attend">参加予定{{else}}">参加未定{{/if}}</div></div>
		</span>
	</div>
</script>
