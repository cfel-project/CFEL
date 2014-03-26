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
use_stylesheet('/opDSLCommonPlugin/css/dsl_common.css', 'last');
use_stylesheet('/opMICGadgetsPlugin/css/gadgets.css', 'last');
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
?>
<?php include("_eventSummary_core.php")?>
<div class="dparts event_summary" id="eventsummary_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
			<h3>イベント一覧</h3>
		</div>
		<div class="prev_link_container"></div>
		<div class="event_container"></div>
		<div class="next_link_container row"></div>
	</div>
</div>
