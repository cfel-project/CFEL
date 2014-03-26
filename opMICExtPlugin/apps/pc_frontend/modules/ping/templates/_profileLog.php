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
include ("_profileLog_core.php");
?>
<div  class="dparts" id ="profile_ping_log_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>最近の様子</h3>
		</div>
		<div class="profile_last_confirmation"></div>
		<div class="profile_confirm_stats_title partsHeading"></div>
		<div class="profile_confirm_stats"></div>
	</div>
</div>
