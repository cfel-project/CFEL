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

class imgexfActions extends opJsonApiActions{
	public function preExecute(){
		parent::preExecute();
		$this->member = $this->getUser()->getMember();
	}
	private function connect(){
		$link = mysql_connect(opMICBridgeConfig::getDBHost().":".opMICBridgeConfig::getDBPort(), opMICBridgeConfig::getDBUser(), opMICBridgeConfig::getDBPass());
		return $link;
	}
	private function selectdb($link){
		return mysql_select_db(opMICBridgeConfig::getDBName(), $link);
	}

	public function executeSearch(sfWebRequest $request){
		$id = $request->getParameter('name');
		$link = $this->connect();//mysql_connect("127.0.0.1:3306", "pneuser", "passw0rd");
		$db = $this->selectdb($link);
$sql = "select b.bin as bin from file as f left join file_bin as b on f.id = b.file_id where f.name='".$id."'";
		$rs = mysql_query($sql);
		$result = array();
		$row = mysql_fetch_assoc($rs);
		if(!empty($row)){
			$filename = tempnam(sys_get_temp_dir(), 'mcbrg');
			file_put_contents($filename, $row["bin"]);
			$exif =  @exif_read_data($filename);
			if (isset($exif['DateTimeOriginal'])) {
				$result['date'] = $this->toISODate($exif['DateTimeOriginal']);
			} else if (isset($exif['DateTimeDigitized'])) {
				$result['date'] = $this->toISODate($exif['DateTimeDigitized']);
			} else if (isset($exif['DateTime'])) {
				$result['date'] = $this->toISODate($exif['DateTime']);
			}
			if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
				$result['location'] = array($this->toDegree($exif["GPSLongitude"]), $this->toDegree($exif["GPSLatitude"]));
			}
			unlink($filename);
		}else{
			mysql_close($link);
			$this->forward404();
		}
		mysql_close($link);
		return $this->renderJSON($result);
	}

	private function toDegree($coords) {
		for ($i = 0; $i < 3; $i++) {
			$part = explode('/', $coords[$i]);
			if (count($part) == 1) {
				$coords[$i] = $part[0];
			} else if (count($part) == 2) {
				$coords[$i] = floatval($part[0]) / floatval($part[1]);
			} else {
				$coords[$i] = 0;
			}
		}
		list($degrees, $minutes, $seconds) = $coords;
		return $degrees + $minutes / 60 + $seconds / 3600;
	}

	private function toISODate($date) {
		$t = DateTime::createFromFormat("Y:m:d H:i:s", $date, new DateTimeZone("Asia/Tokyo"));
		return $t->format('c');
	}

}
