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

class opMICGadgetsUtil{
	public static function downloadFile($file){
		$filename = $file->getOriginalFilename();
		if(false !== strpos($_SERVER["HTTP_USER_AGENT"], "MSIE")){
			$filename = mb_convert_encoding($filename, "SJIS", "UTF-8");
		}
		$filename = str_replace(array("\r", "\n"), "", $filename);

		$bin = $file->getFileBin()->getBin();

		header("Content-Type: ".$file->getType());
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		header("Content-Length: ".strlen($bin));
		echo $bin;
		exit;
	}
}