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
	window.location.href = "<?php echo $baseUrl ?>/top.html?mic.preventcache=<?php echo time()?>#tkn=<?php echo $token ?>&base=<?php echo app_url_for('api', 'homepage')?>";
});
//]]>
</script>
