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
<h2>Settings for DSL gadgets plugin</h2>

<?php if (count($form) - 1): ?>
<form action="<?php echo url_for('opDSLCommonPlugin/index') ?>" method="post">
<table>
<?php echo $form ?>
<tr>
<td colspan="2"><input type="submit" value="<?php echo __('Save') ?>" /></td>
</tr>
</table>
</form>
<?php else: ?>
<p>No item configurable..</p>
<?php endif; ?>

