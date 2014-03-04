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
$(document).ready(function(){
//hide send email link in smt configimage
	$("body#page_member_smtConfigImage .row:has(form)").contents().filter(function(){return this.nodeType==3 || this.tagName.toLowerCase() =="a";}).remove();

//wrap age input box with link to config settings
	var prof_birthhelp = $(".controls:has(input#profile_op_preset_birthday_value_year)>.help-block>.help");
	if(0 < prof_birthhelp.length){
		prof_birthhelp.html(prof_birthhelp.html().replace(/(設定変更|Settings)/,"<a href='<?php echo url_for("member/config")."?category=publicFlag"?>'>$1</a>"));
	}
});
</script>
