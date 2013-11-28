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
<script type="text/javascript">
(function(){
	function __add_user_name_help(){
		$("#member_name").after('<div class="help">「<?php echo $op_config['sns_name']; ?>」で活動を行う上で、常にあなたの名前として表示されるものです。プライパシー保護の観点から、本名を使用しないことをおすすめします。また、記号や数字のみのニックネームなど、読み上げにくい名前はなるべくお避けください。</div>');
	}
	
	function __transform_birth_year(){
		$("#profile_birth_year_value").change(function(){
			var org = $(this).val();
			$(this).val(org.replace(/[０-９]/g, function(str){
				return String.fromCharCode(str.charCodeAt(0) - 0xFEE0);
			}));
		});
	}
	
	$(document).ready(function(){
		__add_user_name_help();
		__transform_birth_year();
	});
})();
</script>
