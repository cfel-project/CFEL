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
use_helper('opAsset');
op_smt_use_stylesheet('/opCommunityTopicPlugin/css/smt-topic.css', 'last');
op_smt_use_stylesheet('/opMICGadgetsPlugin/css/gadgets.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
?>
<?php include("_eventSummary_core.php")?>
<div class="event_summary" id="eventsummary_<?php echo $gadget->id ?>">
	<div class="row">
		<div class="gadget_header span12">イベント一覧</div>
	</div>
	<div class="prev_link_container"></div>
	<div class="event_container"></div>
	<div class="next_link_container row"></div>
</div>
