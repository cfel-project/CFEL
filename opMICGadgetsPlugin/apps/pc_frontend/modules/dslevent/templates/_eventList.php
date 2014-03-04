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
use_stylesheet('/opCommunityTopicPlugin/css/smt-topic.css', 'last');
use_stylesheet('/opMICGadgetsPlugin/css/gadgets.css', 'last');
use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
use_javascript('/opDSLCommonPlugin/js/urlparser.js', 'last');

?>
<?php include("_eventList_core.php")?>
<div class="dparts" id="eventlist_<?php echo $gadget->id ?>">
	<div class="parts">
		<div class="partsHeading">
		  <h3><?php echo (empty($title) ? "予定一覧" : $title)?></h3>
		</div>
		<div class="event_container_list">
			<a href="javascript:" class="event_prev"><?php echo (empty($label_prev) ? "前の日" : $label_prev) ?></a>
			<a style="float:right;" href="javascript:" class="event_next"><?php echo (empty($label_next) ? "次の日" : $label_next) ?></a>
			<div class="event_container"></div>
		</div>
	</div>
</div>
