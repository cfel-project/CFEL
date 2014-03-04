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
?>
<a style="float:right;" href="<?php echo public_path('d_topic/new').'/'.$communityId ?>">新しいトピックを投稿する</a>
<?php
include_component("dsl_common", "smtCommunityTopicList", array(
	"community"=> $community,
));
?>
