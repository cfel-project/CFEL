<?php
/*******************************************************************************
 * Copyright (c) 2011, 2013 IBM Corporation and Others
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
	var __id = "pickfile_<?php echo $gadget->id ?>";

	function __getByClass(cls){
		return $("#" + __id + " ." + cls);
	}

	if(!window.File){
	}else{
		__getByClass("filepick").change(function(){
			var r = new FileReader();
			r.onload = function(evt){
				__getByClass("image")[0].src = r.result;
			};
			var file = this.files[0];//__getByClass("filepick")[0].files[0];
			r.readAsDataURL(file);
		});
	}
});

</script>
<div id="pickfile_<?php echo $gadget->id ?>">
	<div class="filepick_container">
		<input type="file" accept="image/*" class="filepick"/>
		<img class="image" width="200" height="200"/>
		<div class="result"/>
	</div>
	<div class="fileapi_unavailable" style="display:none;">
		File API‚ªŽg‚¦‚Ü‚¹‚ñ
	</div>
</div>
