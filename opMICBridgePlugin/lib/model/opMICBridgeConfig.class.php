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

class opMICBridgeConfig{
	public static function getDBHost(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_db_host", "127.0.0.1");
	}

	public static function getDBPort(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_db_port", "3306");
	}

	public static function getDBName(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_db_name", "pt");
	}
	public static function getDBUser(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_db_user", "openpne");
	}
	public static function getDBPass(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_db_pass", "password");
	}
	public static function getEldLandBaseUrl(){
		return Doctrine::getTable("SnsConfig")->get("op_micbridge_plugin_el_base", "/el");
	}
}