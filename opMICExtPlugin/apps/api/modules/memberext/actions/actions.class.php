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

class memberextActions extends opJsonApiActions{
	public function executeSearch(sfWebRequest $request){
		
		$query = Doctrine::getTable('Member')->createQuery('m')
			->andWhere('m.is_active = true');
		$this->members = $query->execute();
		$res = opMICConfirmationLog::searchLastEntries();
		$this->lastones = array();
		foreach($res as $ent){
			$this->lastones["".$ent["member_id"]] = $ent;
		}
	}
}
