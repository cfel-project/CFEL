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
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
op_smt_use_javascript('/opMICExtPlugin/js/d3.min.js', 'last');
include ("_profileLog_core.php");
?>
<div  class="row" id ="profile_ping_log_<?php echo $gadget->id ?>">
	<div class="gadget_header span12">
		最近の様子
	</div>
	<div class="span12 profile_last_confirmation"></div>
	<div class="gadget_header span12 profile_confirm_stats_title"></div>
	<div class="profile_confirm_stats span12" style="width:60%;margin-left:20%;"></div>
</div>
