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
	$("#linkLine a").each(function(){
		$(this).attr("href", $(this).attr("href").replace(/\/community\//, "/d_topic/listCommunity/"));
		$(this).html("トピック一覧");
	});

	$(".topicDetailBox .operation form[action*='/communityTopic/edit/']").each(function(){
		$(this).attr("action", $(this).attr("action").replace(/\/communityTopic\/edit\//,"/d_topic/edit/"));
	});
<?php if(!empty($pdf_url) && !empty($pdf_name)){ ?>
	$(".topicDetailBox .body p.text").before("<p class='pdf'><a target='_blank' href='<?php echo $pdf_url ?>'><?php echo $pdf_name?></a></p>");
<?php } ?>
});
//]]>
</script>
