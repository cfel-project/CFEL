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
	$("script#topicEntry").each(function(){
		$(this).html($(this).html().replace(/\/communityTopic\/edit\/\$\{id\}/g, "/d_topic/edit/${id}"));
	});
	$("script#topicComment").each(function(){
		$(this).html($(this).html().replace(/class="deleteComment" data-comment-id="\${id}"><i class="icon-remove"><\/i>/, 'class="deleteComment" data-comment-id="${id}">削除する'));
	});
});
//]]>
</script>
