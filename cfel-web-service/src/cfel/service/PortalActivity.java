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

package cfel.service;

import org.apache.wink.json4j.JSONArray;
import org.apache.wink.json4j.JSONException;
import org.apache.wink.json4j.JSONObject;

import cfel.util.WSClientUtil;

public class PortalActivity {

	private JSONObject mParam;

	public PortalActivity() {
		mParam = new JSONObject();
		try {
			mParam.put("count", 20);
			mParam.put("timerCount", 60000);
			mParam.put("image_size", "large");
			mParam.put("apiKey", Config.API_KEY);
		} catch (JSONException e) {
			e.printStackTrace();
		}
	}

	public JSONArray getNext() {
		if (mParam == null) {
			return null;
		}
		JSONArray result = null;
		boolean hasNext = false;
		try {
			JSONObject obj = (JSONObject) WSClientUtil.getJSON(PortalImportTask.ACTIVITY_SEARCH_URL + WSClientUtil.getFormParameter(mParam));
			if (obj != null) {
				String status = obj.getString("status");
				if ("success".equals(status)) {
					result = obj.getJSONArray("data");
					for (int i = 0; i < result.length(); i++) {
						JSONObject activity = result.getJSONObject(i);
						String strId = activity.getString("id");
						int id = Integer.parseInt(strId);
						if (!mParam.containsKey("max_id") || mParam.getInt("max_id") >= id) {
							mParam.put("max_id", id - 1);
							hasNext = true;
						}
					}
					if (result.length() < mParam.getInt("count")) {
						hasNext = false;
					}
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		if (!hasNext) {
			mParam = null;
		}
		return result;
	}
}
