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
op_smt_use_stylesheet('/opMICExtPlugin/css/member_list.css', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
?>

<div class="row" id="ping_member_list_<?php echo $gadget->id ?>">
	<div class="gadget_header span12">
		メンバーの様子一覧
	</div>
	表示順: <select class="sort_option" disabled>
		<option value="last_login_desc" selected>ログイン時刻</option>
		<option value="last_login">ログイン時刻(古い順)</option>
		<option value="last_confirm_desc">安否回答時刻</option>
		<option value="last_confirm">安否回答時刻(古い順)</option>
		<option value="last_confirm_val_desc">安否回答値</option>
		<option value="last_confirm_val">安否回答値(逆順)</option>
	</select>
	<div class="cnfrm_member_list_container">
		読み込み中...
	</div>
</div>
<?php include("__ping_memberlist_core.php")?>
