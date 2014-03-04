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
<?php if ($pageId == $myId): ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
	$("<a>", {"href": "<?php echo url_for("member/image")."/config" ?>"})
	 .html("写真変更")
	 .appendTo(
		$("<p>").appendTo($("#informationAboutThisIsYourProfilePage .body p:has(a)"))
	 );
});
//]]>
</script>
<?php endif; ?>
