<?php
/*******************************************************************************
 * Copyright (c) 2011, 2013 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
?>
<?php
 use_helper('Javascript', 'opUtil', 'opAsset');
 op_smt_use_stylesheet('/opDSLCommonPlugin/css/dsl_topiclist.css', 'last');
 op_smt_use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
 op_smt_use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
 op_smt_use_javascript('/opDSLCommonPlugin/js/urlparser.js', 'last');
?>
<style type="text/css">
.dsl_smt_topics ul{
	list-style:none;
}

.dsl_smt_topics .dsl_author_suffix,
.dsl_smt_topics .moreInfo{
	display:none;
}
</style>
<?php include("_communityTopicList_core.php")?>
<div id="<?php echo $partId ?>" class="dsl_smt_topics"><div class="row">
	<div class="gadget_header"><?php echo (isset($list_title) ? $list_title : 'トピック一覧') ?></div>
<?php if ($acl->isAllowed($sf_user->getMemberId(), null, 'add')): ?>
	<ul class="dsl_topiclist_top_menu">
	</ul>
	<div class="dsl_float_clearfix"></div>
<?php endif; ?>
	<div id="topicList" class="dsl_topiclist" style="margin-left: 0px;">
	</div>
	<ul class="dsl_topiclist_top_menu">
	</ul>
	<div class="dsl_float_clearfix"></div>
</div></div>
