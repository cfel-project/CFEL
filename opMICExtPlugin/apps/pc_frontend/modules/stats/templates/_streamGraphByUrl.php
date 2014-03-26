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
use_helper("Javascript", "opUtil", "opAsset", "opConfirmationLog");
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
use_javascript(opMICExtConfig::getD3URL(), 'last', array(
	"raw_name" => true,
));
?>

<div class="dparts" id="logstat_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>ページごとの訪問数(日毎)</h3>
		</div>
		<div class="logstat_container">
			読み込み中...
		</div>
	</div>
</div>
<?php include("__stream_by_url_core.php")?>
