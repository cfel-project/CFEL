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
//<![CDATA[
$(document).ready(function(){
	$("#timelineTemplate").html(
		$("#timelineTemplate").html()
		 .replace(/ \| /g, "<span> | </span>")
		 .replace(/{{html body_html}}/g, "{{if uri != null}}<a href='<?php echo url_for('dslmisc/forwardUri')?>?uri=${uri}'>{{html body_html}}</a>{{else}}{{html body_html}}{{/if}}")
		 .replace(/<div class="timeline-post">/g, "<div class=\"timeline-post{{if uri !=null}} timeline-with-uri{{/if}}\">")
		 .replace(/<div class="timeline-post-control">/g, "<div style=\"clear:both;\"></div><div class=\"timeline-post-control\"{{if uri != null}} style=\"display:none;\"{{/if}}>")
	);
});
//]]>
</script>
