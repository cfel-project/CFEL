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
$(window).bind("beforeunload", function(e){
	$("<div style='position:fixed;top:0;left:0;width:100%;height:100%; background:rgba(255,255,255,.8'><h1 class='loader' style='text-align:center;margin-top:40%;'>読み込み中...</h1></div>").appendTo($("body"));
});
</script>
