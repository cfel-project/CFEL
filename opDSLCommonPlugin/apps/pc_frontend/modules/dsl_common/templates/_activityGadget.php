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
<div id="dsl_activitylist_<?php echo $gadget->id ?>" class="dsl_activity_gadget">
	<div class="dsl_block" style="overflow:auto;">
		<?php if (count($activities) || isset($form)): ?>
			<?php $params = array(
				  'activities' => $activities,
				  'gadget' => $gadget,
				  'title' => 'みんなのつぶやき',
				  'moreUrl' => 'member/showAllMemberActivity'
				) ?>
			<?php if (isset($form)): ?>
			<?php $params['form'] = $form ?>
			<?php endif; ?>
			<?php include_partial('default/activityBox', $params) ?>
		<?php endif; ?>
		<?php /*include_component('timeline', 'timelineProfile');*/ ?>
	</div>
</div>
