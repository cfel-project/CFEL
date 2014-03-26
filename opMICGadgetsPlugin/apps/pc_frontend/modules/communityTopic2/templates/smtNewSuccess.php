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
include("newSuccess.php");
include("smtTopicFormFix.php");
?>
<script type="text/javascript">
$(document).ready(function(){
	if(window["toggleSubmitState"] && !window["__toggleSubmitState_org"]){
		window["__toggleSubmitState_org"] = toggleSubmitState;
		toggleSubmitState = function(){
			var showtopic = $("#successMessage>a");
			if(0 <showtopic.length){
				window.location.href = showtopic.attr("href");
			}else{
				__toggleSubmitState_org();
			}
		};
	}
});
</script>
