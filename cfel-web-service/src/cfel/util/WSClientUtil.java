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

package cfel.util;

import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.URL;

import org.apache.wink.json4j.JSON;
import org.apache.wink.json4j.JSONObject;

public class WSClientUtil {

	public static Object getJSON(String StrUrl) {
		System.out.println(StrUrl);
		HttpURLConnection conn = null;
		InputStream is = null;
		try {
			URL url = new URL(StrUrl);
			conn = (HttpURLConnection) url.openConnection();
			conn.setConnectTimeout(60 * 1000);
			conn.setRequestMethod("GET");
			conn.setUseCaches(false);
			int responseCode = conn.getResponseCode();
			if (responseCode == HttpURLConnection.HTTP_OK) {
				is = conn.getInputStream();
				if (is != null) {
					return JSON.parse(is);
				}
			} else {
				System.err.println("requestURL: " + url);
				System.err.println("responseCode: " + responseCode);
			}
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			if (conn != null) {
				conn.disconnect();
			}
			if (is != null) {
				try {
					is.close();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
		}
		return null;
	}

	public static String getFormParameter(JSONObject obj) {
		StringBuilder sb = new StringBuilder();
		for (Object key : obj.keySet()) {
			sb.append(String.format("%s%s=%s", sb.length() == 0 ? "?" : "&", key, obj.get(key)));
		}
		return sb.toString();
	}
}
