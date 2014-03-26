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
use_helper('opAsset');
op_smt_use_stylesheet('/opMICGadgetsPlugin/css/communitytopic_form_fix.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opMICGadgetsPlugin/js/communitytopic_form_fix.js', 'last');

?>
<script type="text/javascript">
$(document).ready(function(){
	$("#formCommunityEvent").dsl_communitytopic_form_fix({
		action_src:/\/communityEvent\//,
		action_dst:"/d_event/"
	});
	var __fnode = $("form");
	__fnode.find(".operation input[type=submit]").click(function(ev){
		ev.preventDefault();
		$(".error:has(ul.error_list)").remove();
		var velems = __fnode.find("table tr:has(th>label>strong)").find("[name]:visible,#op_date_input").filter(function(){return !$(this).val();});
		if(0 < velems.length){
			velems.each(function(){
				$(this).before('<div class="error"><ul class="error_list"><li>必須項目です。</li></ul></div>');
			});
		}else{
			__fnode.submit();
		}
	});
});
</script>
