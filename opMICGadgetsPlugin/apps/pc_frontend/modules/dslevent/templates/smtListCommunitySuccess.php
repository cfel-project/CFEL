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
if ($acl->isAllowed($sf_user->getMemberId(), null, 'add')): ?>
<a style="float:right;" href="<?php echo url_for("d_event/new")."/".$communityId ?>"><?php echo __('Create') ?></a>
<?php endif;
include_component("dslevent", "smtEventList", array(
	"targetKey" => "community",
	"targetId" => $communityId,
	"days" => 7,
	"label_prev" => "前の週",
	"label_next" => "次の週",
	"title" => $communityName.": 予定一覧",
));
?>
