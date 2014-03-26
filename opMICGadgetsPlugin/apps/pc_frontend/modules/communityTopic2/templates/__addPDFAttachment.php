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
<script type="text/javascript">
$(document).ready(function(){
	var trg = $("#mic_add_pdf_attachment");
	if(0 == trg.length){
		$("#formCommunityTopic form table tr:last").after('<tr id="mic_add_pdf_attachment"><th><label for="community_topic_pdf">PDF</label></th><td><ul id="community_topic_pdf"><li><input size="40" type="file" accept="application/pdf" name="community_topic_ext[pdf][pdf]" id="community_topic_pdf"></li></ul></td></tr>');

<?php if(!empty($pdf_topic) && !empty($pdf_name)){ ?>
		$("#community_topic_pdf").before("<a target='_blank' href='<?php echo url_for("d_topic/pdf")."/".$pdf_topic ?>'><?php echo $pdf_name?></a><br/>").after("<input size='40' type='checkbox' name='community_topic_pdf_delete' id='community_topic_pdf_delete'><label size='40' for='community_topic_pdf_delete'>このPDFを削除</label>");
<?php } ?>

	}
});
</script>