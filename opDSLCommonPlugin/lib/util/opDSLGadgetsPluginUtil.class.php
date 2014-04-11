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

class opDSLGadgetsPluginUtil{
	public static function getMemberLocation($member){
		$profLoc = $member->getProfile("user_city");
		if(null == $profLoc){
			$profLoc =  $member->getProfile("op_preset_region");
		}
		$ret = "";
		if(null != $profLoc){
			$ret =$profLoc->getValue();
		}
		return $ret;
	}
//openpne seems to be storing the date in date time string of os default..
	public static function DateTimeStrToISO8601($dtstr){
		return date("c", strtotime($dtstr));
	}

	public function getLastTopicVisitTime($member, $topicId){
		return $member->getConfig('lastvtopic_'.$topicId);
	}
	public function updateLastTopicVisitTime($member, $topicId){
		$member->setConfig('lastvtopic_'.$topicId, date('Y-m-d H:i:s'), true);
	}

	private function toWhereClause($prms){
		$wcls = array();
		$wvls = array();

		if(!empty($prms['mid'])){
			array_push($wcls, 'member_id=?');
			array_push($wvls, $prms['mid']);
		}
		if(!empty($prms['cid'])){
			array_push($wcls, 'community_id=?');
			array_push($wvls, $prms['cid']);
		}
		if(!empty($prms['id'])){
			array_push($wcls, 'id=?');
			array_push($wvls, $prms['id']);
		}
		return array(
			'cls' => $wcls,
			'vls' => $wvls,
		);
	}
	public function queryTopics($prms){
		$c = opDSLGadgetsPluginUtil::toWhereClause($prms);
		
		if(0 < count($c['cls'])){
			$query = Doctrine::getTable('CommunityTopic')->createQuery('t')
			 ->where(implode(' AND ', $c['cls']), $c['vls'])
			 ->orderBy($prms['fixed'] ? 'created_at desc' : 'topic_updated_at desc')
			 ->offset($prms['offs'])
			 ->limit($prms['limit']);
			return $query->execute();
		}
		return null;
	}
	public function countTopics($prms){
		$c = opDSLGadgetsPluginUtil::toWhereClause($prms);
		
		if(0 < count($c['cls'])){
			$query = Doctrine::getTable('CommunityTopic')->createQuery('t')
			 ->select('count(*) as total')
			 ->where(implode(' AND ', $c['cls']), $c['vls']);
			$rs = $query->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
			return $rs['total'];

		}
		return 0;
	}
}

