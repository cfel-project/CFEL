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
$title = $u_name."さんのイベント";
$title_menu = array(
	"link"=>url_for("dslevent/list")."?tky=my&dur=w1",
	"text"=>"今週分",
);
include("_smtEventGrid.php");
?>
