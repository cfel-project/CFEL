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
 use_helper('Javascript', 'opUtil', 'opAsset');
 use_stylesheet('/opDSLCommonPlugin/css/dsl_common.css', 'last');
 use_stylesheet('/opDSLCommonPlugin/css/dsl_topiclist.css', 'last');
 use_javascript('/opCommunityTopicPlugin/js/moment.min.js', 'last');
 use_javascript('/opCommunityTopicPlugin/js/lang/ja.js', 'last');
 use_javascript('/opDSLCommonPlugin/js/urlparser.js', 'last');
?>
<?php include("_communityTopicList_core.php")?>
<div id="<?php echo $partId ?>" class="dparts"><div class="parts">
	<div class="partsHeading"><h3><?php echo (isset($list_title) ? $list_title : 'トピック一覧') ?></h3></div>
<?php if ($acl->isAllowed($sf_user->getMemberId(), null, 'add')): ?>
	<ul class="dsl_topiclist_top_menu">
<!--
		<li style="float:right;">
			<a href="<?php echo public_path('communityTopic/new').'/'.$communityId ?>">新しいトピックを投稿する</a>
		</li>
 -->
	</ul>
<?php endif; ?>
	<div id="topicList" class="dsl_topiclist" style="margin-left: 0px;">
	</div>
</div></div>
