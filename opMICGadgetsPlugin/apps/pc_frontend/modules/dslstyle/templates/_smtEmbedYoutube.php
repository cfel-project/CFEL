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
//op_smt_use_javascript('/opMICGadgetsPlugin/js/jq.ltx.youtube.js', 'last');
?>
<script type="text/javascript">
//<![CDATA[
$("<script type='text/javascript' src='<?php echo url_for("communityTopic/")."../opMICGadgetsPlugin/js/jq.ltx.youtube.js" ?>'>").appendTo("head");

$(document).ready(function(){
	(function(){
		var isCommunityTopicPage = function (){
			var paths = ("" + window.location.href).split("/");
			return ("communityTopic" == paths[paths.length - 2]);
		};
		var domNodeInsertedHandler = function() {
			$(this).unbind("DOMNodeInserted");
			$(this).find(".row.body, .comment-body").filter(function(){
				return "true" != $(this).attr("_link_wrap");
			}).each(function(){
				$(this).attr("_link_wrap", "true");

				$(this).dsl_url_text_replace();
				var width = $(this).width();
				$(this).find("a").each(function(){
					$(this).dsl_youtube_link_replace({width:width});
				});
			});
			$(this).bind("DOMNodeInserted", domNodeInsertedHandler);
		};
		if(isCommunityTopicPage()){
			$('div#show').bind("DOMNodeInserted", domNodeInsertedHandler);
		}
	})();
});
//]]>
</script>