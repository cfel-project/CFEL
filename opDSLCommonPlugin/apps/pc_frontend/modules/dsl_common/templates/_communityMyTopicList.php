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
	$acl = opCommunityTopicAclBuilder::buildCollection($community, array($sf_user->getMember()));
?>
<?php if ($acl->isAllowed($sf_user->getMemberId(), null, 'add')): ?>
<?php include("_communityTopicList.php")?>
<?php endif; ?>	
