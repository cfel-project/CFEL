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
	$(".password_query a").attr("href", "<?php echo url_for('simpleAuth/passwordRecovery') ?>").html("パスワードを忘れた方はこちら");
	$("form p.password_query").insertAfter($("form p.password_query").siblings("input:first"));
});
</script>
