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

class dsleventActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
	}

	public function executeSearch(sfWebRequest $request){
		$this->forward400If(empty($request['target']) || '' === (string)$request['target'], 'target is not specified');

		$start = $request->getParameter('start');
		$end = $request->getParameter('end');
		$limit = $request->getParameter('limit');

		$query = Doctrine::getTable('CommunityEvent')->createQuery('q')
			->orderBy('open_date');
		if(!empty($limit)){
			$query->limit($limit);
		}

		if(!empty($start)){
			$query->andWhere('open_date>=?', date("Y-m-d H:i:s", $start/1000));
		}
		if(!empty($end)){
			$query->andWhere('open_date<?', date("Y-m-d H:i:s", $end/1000));
		}
		$memberId = $this->member->id;

		$target = $request->getParameter('target');
		if($target == 'my'){
			$sortKey = $request['skey'];

			if(!empty($sortKey)){
				$sortKey = 'open_date';
			}
			$communities = Doctrine::getTable('CommunityMember')->createQuery('q')
				->select('community_id')
				->where('member_id = ?', $memberId)
				->setHydrationMode(Doctrine::HYDRATE_ARRAY)
				->execute();

			$communityIds = array();
			foreach($communities as $_community){
				array_push($communityIds, $_community['community_id']);
			}
			$query->andWhereIn('community_id', $communityIds);

		}else if($target == 'community'){
			$targetId = $request->getParameter('target_id');
			$this->forward400If(empty($targetId) || '' === (string)$targetId, 'community id is not specified');

			$query->andWhere('community_id = ?', $targetId);
		}else{
			$this->forward404();
		}

		$this->events = $query->execute();
		$this->memberId = $memberId;
	}

	public function executeSearchOrg(sfWebRequest $request){
		$this->forward400If(empty($request['target']) || '' === (string)$request['target'], 'target is not specified');
		$limit = !empty($request['count']) ? $request['count'] : sfConfig::get('op_json_api_limit', 15);

		if ($request['target'] == 'my')
		{
			$memberId = $this->member->id;

			$events = Doctrine::getTable('CommunityEventMember')->createQuery('q')
				->select('community_event_id')
				->where('member_id = ?', $memberId)
				->setHydrationMode(Doctrine::HYDRATE_ARRAY)
				->execute();
			
			$eventIds = array();
			foreach($events as $_event)
			{
				array_push($eventIds, $_event['community_event_id']);
			}
		
			$events = Doctrine::getTable('CommunityEvent')->createQuery('q')
				->whereIn('id', $eventIds)
				->orderBy('event_updated_at desc')
				->limit($limit)
				->execute();

			$this->events = $events;
			$this->memberId = $memberId;
		}

/*		if (!empty($request['format']) && $request['format'] == 'mini')
		{
			$this->setTemplate('searchMini');
		}*/
	}
	
	protected function getCommentInText($comment){
		return $comment->getMember()->getName().': '.$comment->getBody();
	}	
	protected function getComments($eventid){
		$query = Doctrine::getTable('CommunityEventComment')->createQuery('c')
		->where('community_event_id = ?', $eventid)
		->orderBy('created_at asc');
		$comments = $query->execute();
		$atxt = array();
		foreach($comments as $comment){
			array_push($atxt, $this->getCommentInText($comment));
		}
		return $atxt;
	}
	public function executeReportEvent(sfWebRequest $request){
		if("POST" == $request->getMethod()){
			$eventId = $request['eid'];
			$communityId = $request['cid'];
			$community = Doctrine::getTable('Community')->find($communityId);
			$this->event = Doctrine::getTable('CommunityEvent')->find($eventId);
			if($this->event->getMember()->getId() == $this->member->getId()){
				$members = $community->getMembers();
				$addrs = array();
				foreach($members as $member) {
					array_push($addrs, $member->getEmailAddress());
				}
				$this->sendObj = array(
						'to' => implode(",", $addrs),
						'from' => $this->member->getEmailAddress(),
						'subject' => '[イベント情報の更新通知] '.$this->event->getName(),
						'path' => 'communityEvent/'.$eventId,
						'comments' => implode("\n", $this->getComments($eventId))
				);
			}else{
				$this->forward401('request is allowed only topic author');
			}
		}
	}
	public function executeGetEditLink(sfWebRequest $request){
		$eventId = $request["id"];
		$this->memberId = $this->member->getId();
		if(!empty($eventId)){
			$this->event = Doctrine::getTable('CommunityEvent')->find($eventId);
		}
	}
	public function executeDelete(sfWebRequest $request){
		$request->checkCSRFProtection();
		$eventId = $request["id"];
		$this->communityEvent = Doctrine::getTable("CommunityEvent")->find($eventId);
		$this->forward404Unless($this->communityEvent->isEditable($this->member->getId()));
		$this->communityEvent->delete();

		$this->communityId = $this->communityEvent->getCommunity()->getId();
	}
}
