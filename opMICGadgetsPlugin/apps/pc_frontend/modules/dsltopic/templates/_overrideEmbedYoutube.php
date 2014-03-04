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
use_helper('Javascript', 'opUtil', 'opAsset');
use_javascript('/opMICGadgetsPlugin/js/jq.ltx.youtube.js', 'last');
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$(".body embed").each(function(){
		$(this).dsl_src_protocol_fix();
	});//full screen of komado does not work...
	$(".body a").each(function(){
		$(this).dsl_youtube_link_replace({width:$(this).parent().width()});
	});
});
//]]>
</script>