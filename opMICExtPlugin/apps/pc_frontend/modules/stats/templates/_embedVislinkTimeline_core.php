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
	var q_option = [];
	$.each(<?php echo htmlspecialchars_decode($q_opt_json)?>, function(k,val){
		q_option.push(k + "=" + val);
	});
	var v_link_url =  "<?php echo $timeline_vis_href?>" + (q_option.length > 0 ? "?" + q_option.join("&") : "");
	setTimeout(function(){
		$(".me_timeline_vis_link").each(function(){
			var btn = $("<button/>", {
				"class": "me_btn",
				"style": "cursor:pointer;"
			}).width($(this).innerWidth()).height($(this).innerHeight() - 2)
			.click(function(){
				window.location.href = v_link_url;
			}).appendTo($(this));
			var sz = Math.min(btn.innerHeight(), btn.innerWidth());
			btn.append(
				$("<img/>", {
					"src": "<?php echo url_for('opMICExtPlugin/images')?>/tlrel_vis_sm.png"
				}).width(sz).height(sz)
			);
		});
	},100);
});
//]]>
</script>
