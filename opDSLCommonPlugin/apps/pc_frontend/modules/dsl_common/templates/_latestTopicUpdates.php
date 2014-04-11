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
<?php if (count($topics)): ?>
<script type="text/javascript">
$(document).ready(function(){
	var rt = $("#homeRecentList_<?php echo $gadget->getId() ?>");
	$(".searchFormLine input[name='search_button']", rt).click(function(){
		var text = $(".searchFormLine input[name='search_query']").attr("value");
		if(text){
			window.location.href = "<?php echo public_path('search') ?>?search=action&search_module=dsl&search_query=" + text;
		}
	});
});
</script>
<div id="homeRecentList_<?php echo $gadget->getId() ?>" class="dsl_updatedTopics dparts homeRecentList"><div class="parts">
<div class="partsHeading"><h3><?php echo __('Recently Posted %Community% Topics') ?></h3></div>
<div class="searchFormLine">
	<ul>
		<li>
			<input type="text" style="width:80%;" class="input_text" name="search_query" title="<?php echo __('%Community%書き込み検索') ?>"/>
		</li>
		<li>
			<input type="button" name="search_button" class="input_submit" value="<?php echo __('Search') ?>"/>
		</li>
	</ul>
</div>
<div class="block">
<ul class="articleList">
<?php foreach ($topics as $topic): ?>
<li><span class="date"><?php echo op_format_date($topic->getUpdatedAt(), 'XShortDateJa') ?></span>
<?php echo sprintf('%s (%s)',
  link_to(sprintf('%s(%d)',
    op_truncate($topic->getName(), 36),
    $topic->getCommunityTopicComment()->count()
  ), '@communityTopic_show?id='.$topic->getId()),
  $topic->getCommunity()->getName()
) ?></li>
<?php endforeach; ?>
</ul>
<div class="moreInfo">
<ul class="moreInfo">
<li><?php echo link_to(__('More'), 'communityTopic_recently_topic_list') ?></li>
</ul>
</div>
</div>
</div></div>
<?php endif; ?>
