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

class activityextActions extends opJsonApiActions{

	protected function getFileinfo($request, $tmpfile){
		$metadata = stream_get_meta_data($tmpfile);
		$filename = $request['filename'];
		$tmp_name = $metadata['uri'];
		$sdata = $request['data'];
		$data = base64_decode(substr($sdata, strpos($sdata, ",") + 1));
		fwrite($tmpfile, $data);
		if("image/jpeg" === $request["type"]){
//rotate image by orientation exif from iPhone/iPad
			$exif_props = @exif_read_data($tmp_name);
			$rotate_map = array(
				3 => 180,
				6 => 270,
				8 => 90,
			);
			$rotate = $rotate_map[$exif_props["Orientation"]];
			if($rotate){
//note the exif information is lost by this transformation. may pel or some other libraries be used.
				$src = imagecreatefromjpeg($tmp_name);
				$dst = imagerotate($src, $rotate, 0);
				imagejpeg($dst, $tmp_name, 100);
				imagedestroy($src);
				imagedestroy($dst);
//				$data = fread($tmpfile, filesize($tmp_name));
			}
		}
		return array(
			'name' => $filename,
			'tmp_name' => $tmp_name,
			'web_base_path' => $request->getUriPrefix().$request->getRelativeUrlRoot(),
			'type' => $request['type'],
			'member_id' => $this->getUser()->getMemberId(),
			'binary' => $data,//note this binary is raw data from client.
			'size' => strlen($data)
		);
	}
	protected function isValidUpload(sfWebRequest $r){
		return isset($r['type']) && isset($r['filename']) && isset($r['data']);
	}
	protected function getOpTimelineInstance($request){
		$user = new opTimelineUser();
		$params = array(
			'image_size' => $this->getRequestParameter('image_size', 'large'),
			'base_url' => $request->getUriPrefix().$request->getRelativeUrlRoot(),
		);
		return new opTimeline($user, $params);
	}
	public function executePostImage(sfWebRequest $request){
		$target = $request['target'];
		$trg_id = $request['target_id'];
		if("POST" == $request->getMethod() && isset($trg_id) && $this->isValidUpload($request)){
			$tmp = tmpfile();
			$fileinfo = $this->getFileInfo($request, $tmp);
		 	$timeline = $this->getOpTimelineInstance($request);
			$activity_image = $timeline->createActivityImageByFileInfoAndActivityId($fileinfo, $trg_id);
			fclose($tmp);
			$ret = array(
				'status' => 'success',
				'id' => $activity_image->getId(),
				'uri' => $activity_image->getUri(),
			);
			return $this->renderJSON($ret);
		}else{
			$this->forward400('insufficient request param or form');
		}
	}
	public function executePostWithImage(sfWebRequest $request){
		if("POST" == $request->getMethod() && $this->isValidUpload($request)){
			$tmp = tmpfile();
			$fileinfo = $this->getFileInfo($request, $tmp);
			$_FILES['timeline-submit-upload'] = $fileinfo;
		}
		$this->forward("activity", "post");
	}
}
