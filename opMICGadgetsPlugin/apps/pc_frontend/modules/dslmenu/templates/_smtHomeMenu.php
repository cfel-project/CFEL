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
op_smt_use_stylesheet('/opMICGadgetsPlugin/css/home_menu.css', 'last');
?>
<?php include("_homeMenu_core.php")?>
<div id="home_menu_<?php echo $gadget->id ?>" style="margin:.4em 0;">
	<div class="row">
<!--	  <div class="gadget_header span12">メニュー</div> -->
	</div>
	<div class="hm_menu_container">
	</div>
</div>
