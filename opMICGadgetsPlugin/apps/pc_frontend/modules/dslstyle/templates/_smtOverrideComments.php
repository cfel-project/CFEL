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
// op_smt_use_stylesheet('/opMICGadgetsPlugin/css/comments_override.css', 'last');
?>
<script type="text/javascript">
//<![CDATA[
(function(){
	$("<link rel='stylesheet' type='text/css' media='screen' href='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/css/comments_override.css" ?>'>").appendTo("head");
//override timeline picture link
	if(window["renderJSON"] && !window["__renderJSON_org"]){
		window["__renderJSON_org"] = renderJSON;
		renderJSON = function(json, mode){
			__renderJSON_org(json, mode);
			$(".timeline-post a:has(div>img)").each(function(){
				$(this).attr("href", $(this).attr("href").replace(/\/w285_h285\//,"/w_h/"));
			});
		};
	}

})();
//]]>
</script>
