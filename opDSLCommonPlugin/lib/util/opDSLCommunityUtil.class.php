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

class opDSLCommunityUtil{
	public static function getCommunityListByIDs($ids){
		return Doctrine::getTable('Community')->createQuery()
		->whereIn('id', $ids)
		->orderBy('created_at')
		->execute();
	}
	public static function getCommunityByID($id){
		$comms = Doctrine::getTable('Community')->createQuery()->where('id = ?', $id)->execute();
		return $comms[0];
	}
	
	public static function getJoinCommunityList($memberId){
		$communityMembers = Doctrine::getTable('CommunityMember')->createQuery()
		->select('community_id')
		->where('member_id = ?', $memberId)
		->andWhere('is_pre = ?', false)
		->execute(array(), Doctrine::HYDRATE_NONE);
	
		$ids = array();
		foreach ($communityMembers as $communityMember){
			$ids[] = $communityMember[0];
		}
	
		return opDSLCommunityUtil::getCommunityListByIDs($ids);
	}
	
}