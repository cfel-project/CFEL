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
class dsl_commonComponents extends sfComponents
{
	public function executeMapGadget(sfWebRequest $request)
	{
//		$culture = sfCultureInfo::getInstance($sfUser->getCulture());

		$member = $this->getUser()->getMember();
		$this->u_name = $member->getName();
		$profLoc = $member->getProfile("user_city");
		if(null == $profLoc){
			$profLoc =  $member->getProfile("op_preset_region");
		}
		if(null != $profLoc){
			$this->u_homeLocation =$profLoc->getValue(); 
		}
//		if(true)//($profLoc->getProfile()->isPreset() && 'country_select' === $profLoc->getFormType())
//		{
//		 $this->u_homeCaption = $culture->getCountry($profLoc);//__($culture->getCountry((string)$profLoc));
//		}else{
			$this->u_homeCaption = $this->u_homeLocation;
//		}

//		$this->setDiary();
//		$this->setTopicCommentListInstance();
//		$this->setAllMemberActivityList();
	}

	public function executeTimelineSidebar(sfWebRequest $request)
	{
		$this->getResponse()->addStyleSheet('/opTimelinePlugin/css/jquery.colorbox.css');
		$this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.colorbox.js', 'last');
		$this->getResponse()->addJavascript('/opTimelinePlugin/js/jquery.timeline.js', 'last');
		$this->memberId = $request->getParameter('id', $this->getUser()->getMember()->getId());

		$this->viewPhoto = opTimeline::getViewPhoto();

		$this->setFileMaxSize();

		return sfView::SUCCESS;
	}
	
	public function executeCommunityTopicList(sfWebRequest $request){
		$this->communityId = $this->community->getId();
		$strcfg = trim(opDSLCommonConfig::getCommunityTopicListConfig());
		$cfg = json_decode($strcfg, true);
		$entry = $cfg['com_'.$this->communityId];
		$this->per_page = (isset($entry) && isset($entry["per_page"])) ? $entry["per_page"] : 10;
		$this->fixed_order = (isset($entry) && isset($entry["fixed_order"])) ? $entry["fixed_order"] : null;
		$this->list_title = 'すべてのトピック';
		if(!isset($this->gadget)){
			$this->partId='dsl_community_topic_list';
		}
	}
	public function executeSmtCommunityTopicList(sfWebRequest $request){
		$this->executeCommunityTopicList($request);
		return sfView::SUCCESS;
	}

	public function executeCommunityMyTopicList(sfWebRequest $request){
//		$this->executeCommunityTopicList($request);
		$this->communityId = $this->community->getId();
		$strcfg = trim(opDSLCommonConfig::getCommunityTopicListConfig());
		$cfg = json_decode($strcfg, true);
		$entry = $cfg['com_'.$this->communityId];
		$this->per_page = (isset($entry) && isset($entry["my_per_page"])) ? $entry["my_per_page"] : 4;
		$this->fixed_order = (isset($entry) && isset($entry["my_fixed_order"])) ? $entry["my_fixed_order"] : null;
		$this->req_mid = $this->getUser()->getMember()->getId();
		$this->list_title = 'あなたのトピック';
		if(!isset($this->gadget)){
			$this->partId='dsl_community_my_topic_list';
		}
	}
	public function executePageLogger(sfWebRequest $request){
		$this->elemLoggerConfig = opDSLCommonConfig::getElementLoggerConfig();
	}
	public function executeSmtPageLogger(sfWebRequest $request){
		$this->ev_sfx = "_smt";
		$this->elemLoggerConfig = opDSLCommonConfig::getElementLoggerConfig();
	}
	public function executeLatestTopicUpdates(sfWebRequest $request){
	    $this->topics = Doctrine::getTable('CommunityTopic')->retrivesByMemberId($this->getUser()->getMember()->getId(), 4);
	}

	public function executeActivityGadget(sfWebRequest $request){
		$this->member = $this->getUser()->getMember();
		$this->activities = Doctrine::getTable('ActivityData')->getAllMemberActivityList(10);//$this->gadget->getConfig('row'));
		if(true)/* ($this->gadget->getConfig('is_viewable_activity_form') && opConfig::get('is_allow_post_activity'))*/{
			$this->form = new ActivityDataForm();
		}
	}
	public function executeAddCommunityName4NewTopic(sfWebRequest $request){
		$this->commName = $this->community->getName();
	}
	public function executeCommunityTopicLogger(sfWebRequest $request){
		opDSLGadgetsPluginUtil::updateLastTopicVisitTime($this->getUser()->getMember(), $this->communityTopic->getId());
	}
	public function executeCommunityTopicSender(sfWebRequest $request){
		if(isset($this->communityTopic)
			 && $this->getUser()->getMember()->getId() == $this->communityTopic->getMember()->getId()){
			$commId = isset($this->community) ? $this->community->getId() : $this->communityTopic->getCommunity()->getId();
			$strcfg = trim(opDSLCommonConfig::getSendTopicCommunityList());
			$cfg = json_decode($strcfg, true);
			$dfaddr = $cfg["addr"];
			$entry = $cfg['com_'.$commId];
			if(isset($entry)){
				$this->member = $this->getUser()->getMember();
				$this->ebtoken =$this->getUser()->getMemberApiKey();
				$this->ebisUrl = opDSLGadgetsConfig::getEBISUrl();
				$this->sendParam = array(
					'commId' => $commId,
					'topicId' => $this->communityTopic->getId(),
					'to' => isset($entry["addr"]) ? $entry["addr"] : $dfaddr,
					'caption' => $entry["cap"],
					'message' => $entry["msg"],
				);
			}
		}
	}
	public function executeFocusTopicComment(sfWebRequest $request){
	}
	public function executeTakePicture(sfWebRequest $request){
	}
}
