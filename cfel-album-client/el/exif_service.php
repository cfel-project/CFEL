<?php 
/*******************************************************************************
 * Copyright (c) 2014 IBM Corporation and Others
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html
 *
 * Contributors:
 *   IBM Corporation - initial API and implementation
 *******************************************************************************/
 
$result = [];
if (isset($_GET['src'])) {
	$exif = @exif_read_data($_GET['src']);
	if (isset($exif['DateTimeOriginal'])) {
		$result['date'] = toISODate($exif['DateTimeOriginal']);
	} else if (isset($exif['DateTimeDigitized'])) {
		$result['date'] = toISODate($exif['DateTimeDigitized']);
	} else if (isset($exif['DateTime'])) {
		$result['date'] = toISODate($exif['DateTime']);
	}
	if (isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])) {
		$result['location'] = [toDegree($exif["GPSLongitude"]), toDegree($exif["GPSLatitude"])];
	}
}
echo json_encode($result);

function toDegree($coords) {
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

function toISODate($date) {
	$t = DateTime::createFromFormat("Y:m:d H:i:s", $date, new DateTimeZone("Asia/Tokyo"));
	return $t->format('c');
}

?>