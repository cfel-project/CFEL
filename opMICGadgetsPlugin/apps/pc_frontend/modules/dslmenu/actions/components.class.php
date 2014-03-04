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
class dslmenuComponents extends sfComponents{
	protected function setHomeMenuData(sfWebRequest $request){
		$strcfg = trim(opMICGadgetsConfig::getHomeMenuConfig());
		$cfg = json_decode($strcfg, true);
		$this->menuItems = array(
			"items" =>  array_map(function($itm){
				if(isset($itm["comm_id"])){
					$comm = opDSLCommunityUtil::getCommunityByID($itm["comm_id"]);
					if(!isset($itm["path"])){
						$itm["path"] = "community/".$comm->getId();
					}
					if(!isset($itm["html"])){
						$itm["html"] = $comm->getName();
					}
					if(!isset($itm["image_file"])){
						$itm["image_file"] = $comm->getImageFileName();
					}
				}
				if(!isset($itm["href"]) && isset($itm["path"])){
					$itm["href"] = public_path($itm["path"], true);
				}
				if(!isset($itm["image"]) && isset($itm["image_file"])){
					$itm["image"] = sf_image_path($itm["image_file"], array(), true);
				}
				return $itm;
			}, $cfg),
		);
		$this->menuItems["num"] = count($cfg);
		$this->menuJson = json_encode($this->menuItems,JSON_UNESCAPED_UNICODE);
	}
	public function executeHomeMenu(sfWebRequest $request){
		$this->setHomeMenuData($request);
	}
	public function executeSmtHomeMenu(sfWebRequest $request){
		$this->setHomeMenuData($request);
	}
}
