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
use_javascript('/opDSLCommonPlugin/js/urlparser.js', 'last');
include("_eventGrid_core.php");
?>
<div class="dparts" id="eventgrid_<?php echo $gadget->id ?>">
	<div class="dsl_block parts">
<?php if(!empty($title)){ ?>
		<div class="partsHeading">
			<h3><?php echo $title ?></h3>
		</div>
<?php } ?>
		<div class="dsl_block event_container_row">
			<div class="dsl_block event_container"></div>
			<a alt="前へ" class="event_prev"><div style="position:absolute;left:-.8em;top:3em;" class="triangle_left"></div></a>
			<a alt="後へ" class="event_next"><div style="position:absolute;right:-.8em;top:3em;" class="triangle_right"></div></a>
		</div>
	</div>
</div>
