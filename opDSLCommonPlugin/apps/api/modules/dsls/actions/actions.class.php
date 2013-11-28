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
class dslsActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
		if(!isset($this->member)){
			$this->forward401('unauthorized API call.');
		}
	}

	public function executeAddLog(sfWebRequest $request){
		$logEntry = new PageLog();
		$logEntry->setMemberId($this->getUser()->getMemberId());
		$logEntry->setUrl($request->getReferer());
		$logEntry->setEvent($request['event']);
//		$gmpEpoc = gmp_init($request['epoc']);
//		$ctime = new DateTime();
//		$ctime->setTimestamp($gmpEpoc);
		$logEntry->setClientEpoc($request['epoc']);
		$logEntry->save();
		$ret = array(
			"status" => "success",
		);
		return $this->renderJSON($ret);
	}
	public function executeAddElemLog(sfWebRequest $request){
		$logEntry = new ElementLog();
		$logEntry->setMemberId($this->getUser()->getMemberId());
		$logEntry->setUrl($request->getReferer());
		$logEntry->setEvent($request['event']);
		$logEntry->setClientEpoc($request['epoc']);
		$logEntry->setSelector($request['selector']);
		if(isset($request['props'])){
			$logEntry->setProps(json_encode($request['props']));
		}
		$logEntry->save();
		$ret = array(
			"status" => "success",
		);
		return $this->renderJSON($ret);
	}

	public function executeActivities(sfWebRequest $request){
		$q = Doctrine::getTable('ActivityData')->getOrderdQuery();
		Doctrine::getTable('ActivityData')->addAllMemberActivityQuery($q);
		if(isset($request['since'])){
			$q->addWhere('updated_at > ?', $request['since']);
		}
		if(isset($request['size'])){
			$limit = intval($request['size']);
			$q->limit($limit);
		}
		$activities = $q->execute();
		
		$results=array();
		foreach ($activities as $activity) {
			$m = array();
			$m["id"] = $activity->getId();
			$m["uri"] = $activity->getUri();
			$m["author"] = $this->member2object($activity->getMember());
			$m["body"] = $activity->getBody();
			$m["since"] = $activity->getUpdatedAt();
			$m["created"] = opDSLGadgetsPluginUtil::DateTimeStrToISO8601($activity->getCreatedAt());
			$m["updated"] = opDSLGadgetsPluginUtil::DateTimeStrToISO8601($activity->getUpdatedAt());
			$m["parent"] = $this->getRT($activity);
			$m["source"] = $activity->getSource();
			$m["source_uri"] = $activity->getSourceUri();
			$m["foreign_table"] = $activity->getForeignTable();
			$m["foreign_id"] = $activity->getForeignId();
			$m["template"] = $activity->getTemplate();
//			$m["activity_num"] = count($activity->getActivityData());
			$m["image_num"] = count($activity->getImages());
			$m["replies"] = count($activity->getReplies());
			array_push($results, $m);
		}
		$ret = array(
			"num" => count($activities),
			"items" => $results
		);
		return $this->renderJSON($ret);
	}


	protected function getRT($activity){
		$rtid = $activity->getInReplyToActivityId();
		if(!is_null($rtid)){
			$activity = $this->getActivityDataById($rtid);
			if(isset($activity)){
				return array(
					"id" => $rtid,
					"body" => $activity->getBody()
				);
			}
		}
		return null;
	}
	protected function getActivityDataById($id){
		$q = Doctrine::getTable('ActivityData')->getOrderdQuery();
		Doctrine::getTable('ActivityData')->addAllMemberActivityQuery($q);
		$q->addWhere('id = ?', id);
		$q->limit(1);
		$activities = $q->execute();
		if(isset($activities)){
			return $activities[0];
		}
		return null;
	}

	protected function getAllMemberActivityList($size = 5){
		return Doctrine::getTable('ActivityData')->getAllMemberActivityList($size);
	}

	protected function member2object($member){
		return array(
			"id" => $member->getId(),
			"name" => $this->member->getName(),
			"location" => opDSLGadgetsPluginUtil::getMemberLocation($this->member)
		);
	}

	protected function getDiaryList($max = 5){
		return Doctrine::getTable('Diary')->getDiaryList($max);
	}
	protected function setTopicCommentListInstance($max = 5)
	{
		return  Doctrine::getTable('CommunityTopic')->retrivesByMemberId($this->getUser()->getMember()->getId(), $max);
	}

	public function executeTopics(sfWebRequest $request){
		$limit = isset($request['count']) ? $request['count'] : sfConfig::get('op_json_api_limit', 15);
		$fixed = !!$request['fixed'];
		$this->offs = isset($request['offs']) ? $request['offs'] : 0;
		$params = array(
			'limit' => $limit,
			'fixed' => $fixed,
			'offs' => $this->offs,
			'mid' => $request['mid'],
			'cid' => $request['cid'],
			'id' => $request['id'],
		);

		$this->topics = opDSLGadgetsPluginUtil::queryTopics($params);
		$this->topics_total =  opDSLGadgetsPluginUtil::countTopics($params);
	}

	protected function getCommentInText($comment){
		return $comment->getMember()->getName().': '.$comment->getBody();
	}
	protected function getComments($topicid){
		$query = Doctrine::getTable('CommunityTopicComment')->createQuery('c')
		 ->where('community_topic_id = ?', $topicid)
		 ->orderBy('created_at asc');
		$comments = $query->execute();
		$atxt = array();
		foreach($comments as $comment){
			array_push($atxt, $this->getCommentInText($comment));
		}
		return $atxt;
	}
	protected function getCommentsInText($topicid){
		return implode("<br/>", $this->getComments($topicid));
	}
	public function executeReportTopic(sfWebRequest $request){
		if("POST" == $request->getMethod()){
			$topicId = $request['tid'];
			$communityId = $request['cid'];
			$msg = $request['msg'];
			$res = $request['res'];
			$strcfg = trim(opDSLCommonConfig::getSendTopicCommunityList());
			$cfg = json_decode($strcfg, true);
			$entry = $cfg['com_'.$communityId];
			if(isset($entry)){
				$community = Doctrine::getTable('Community')->find($communityId);
				$this->topic = Doctrine::getTable('CommunityTopic')->find($topicId);
				if($this->topic->getMember()->getId() == $this->member->getId()){
					$this->sendObj = array(
						'to' => isset($entry["addr"]) ? $entry["addr"] : $cfg["addr"],
						'from' => $this->member->getEmailAddress(),
						'subject' => '['.(isset($entry["sbj"]) ? $entry["sbj"] : $entry["cap"]).'] '.$this->member->getName().': '.$community->getName(),
						'path' => 'communityTopic/'.$topicId,
						'msg' => $msg ? $msg : $entry["msg"],
						'comments' => implode("\n\n", $this->getComments($topicId)),
						'res' => $res ? $res : $entry["res"]
					);
				}else{
					$this->forward401('request is allowed only topic author');
				}
			}else{
				$this->forward404('request for mis-configured community');
			}
		}
	}

	public function executeListPushEvents(sfWebRequest $request){
		$unreadonly  = $request['uronly'];
		$this->member = $this->getUser()->getMember();
		$nfs = opNotificationCenter::getNotifications($this->member);

		$this->notifications = array();
		foreach ($nfs as $n){
			if("true" != $request['uronly'] || $n['unread']){
				array_push($this->notifications, $n);
			}
		}
	}
	public function executeComments(sfWebRequest $request){
		$this->forward400If('' === (string)$request['community_topic_id'], 'community_topic_id parameter is not specified.');

		$topic = Doctrine::getTable('CommunityTopic')->findOneById($request['community_topic_id']);

		$topic->actAs('opIsCreatableCommunityTopicBehavior');
		$this->forward400If(false === $topic->isViewableCommunityTopic($topic->getCommunity(), $this->member->getId()), 'you are not allowed to view this topic and comments on this community');

		$limit = isset($request['count']) ? $request['count'] : sfConfig::get('op_json_api_limit', 15);

		$query = Doctrine::getTable('CommunityTopicComment')->createQuery('c')
			->where('community_topic_id = ?', $topic->getId())
			->orderBy('created_at desc')
			->limit($limit);

		if(isset($request['max_id'])){
			$query->addWhere('id <= ?', $request['max_id']);
		}

		if(isset($request['since_id'])){
			$query->addWhere('id > ?', $request['since_id']);
		}
		$this->memberId = $this->getUser()->getMemberId();
		$this->comments = $query->execute();
	}
	
	public function executeGetLiveStat(sfWebRequest $request){
		$ret = array(
			"logged_in" => count(preg_grep('/SNSMember/', array_map('file_get_contents', glob(sprintf('%s%ssess_*', session_save_path(), DIRECTORY_SEPARATOR))))),
		);
		return $this->renderJSON($ret);
	}
}