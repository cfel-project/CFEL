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
op_smt_use_stylesheet('/opDSLCommonPlugin/css/dsl_common.css', 'last');
op_smt_use_stylesheet('/opMICGadgetsPlugin/css/gadgets.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
op_smt_use_javascript('/opDSLCommonPlugin/js/urlparser.js', 'last');
include("_eventGrid_core.php");
?>
<div class="smt_grid" id="eventgrid_<?php echo $gadget->id ?>">
<?php if(!empty($title)) :?>
	<div class="row">
		<div class="gadget_header span12"><?php echo $title ?></div>
		<?php if(!empty($title_menu)) :?>
			<a style="float:right;" href="<?php echo $title_menu["link"]?>"><?php echo $title_menu["text"] ?></a>
		<?php endif; ?>
	</div>
<?php endif; ?>
	<div style="margin-top:.2em;" class="dsl_block event_container_row">
		<div class="dsl_block event_container"></div>
		<a alt="前へ" class="event_prev"><div style="position:absolute;left:-.8em;top:2em;" class="triangle_left"></div></a>
		<a alt="後へ" class="event_next"><div style="position:absolute;right:-.8em;top:2em;" class="triangle_right"></div></a>
	</div>
</div>
