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

package cfel.servlet;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.HashMap;
import java.util.zip.ZipEntry;
import java.util.zip.ZipOutputStream;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import org.apache.wink.json4j.JSON;
import org.apache.wink.json4j.JSONArray;
import org.apache.wink.json4j.JSONException;
import org.apache.wink.json4j.JSONObject;

import com.mongodb.DBCursor;
import com.mongodb.DBObject;

import cfel.service.Config;
import cfel.util.DBCollectionUtil;
import cfel.util.DBCollectionUtilException;

/**
 * Servlet implementation class AlbumDownloadServlet
 */
@WebServlet("/cfel_service/album_download")
public class AlbumDownloadServlet extends HttpServlet {
	private static final long serialVersionUID = 1L;
	private static final String HEADER_API_KEY = "X-Eld-Land-APIKey";
	private static final String CSV_HEADER = "\"ファイル名\",\"タイトル\",\"年代\",\"季節\",\"場所\"\r\n";
	private static final String CSV_FORMAT = "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\r\n";
	private static final String README_TEXT = "[補足説明]\r\n" + "- index.csvファイルにアルバムに関する情報がカンマ区切り形式で収納されています。\r\n"
			+ "- Microsoft Excel、あるいは、テキストエディタを使用してアルバム情報を閲覧することが可能です。\r\n";

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse
	 *      response)
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		try {
			if (download(request, response)) {
				response.setStatus(HttpServletResponse.SC_OK);
			} else {
				response.sendError(HttpServletResponse.SC_BAD_REQUEST);
			}
		} catch (NullPointerException e) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, e.getLocalizedMessage());
		} catch (DBCollectionUtilException e) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, e.getLocalizedMessage());
		} catch (JSONException e) {
			response.sendError(HttpServletResponse.SC_BAD_REQUEST, e.getLocalizedMessage());
		}
	}
	private static final HashMap<String, String> _options = new HashMap<String, String>(){
		private static final long serialVersionUID = -8075950473127776224L;

		{
			put("query", "{removed:{$ne:true}}");
			put("keys", "{portal_image_url:1,title:1,annotation_title:1,annotation_season:1,annotation_location:1,annotation_date:1}");
		}
	
	};

	private static boolean download(HttpServletRequest request, HttpServletResponse response) throws DBCollectionUtilException, NullPointerException, JSONException {
		String groupBy = request.getParameter("type");
		String apiKey = request.getParameter("apiKey");
		if (apiKey == null || !groupBy.matches("(season|date|location)")) {
			return false;
		}
		String json = DBCollectionUtil.query_serialized("photo", _options);//TODO possibly be converted in direct.
		Object obj = JSON.parse(json);
//		String apiRoot = request.getRequestURL().toString().replace(request.getServletPath(), "");
//		Object obj = getJSON(apiRoot + "/photo" + "?query={removed:{$ne:true}}"
//				+ "&keys={portal_image_url:1,title:1,annotation_title:1,annotation_season:1,annotation_location:1,annotation_date:1}", apiKey);
		if (obj instanceof JSONArray) {
			JSONArray array = (JSONArray) obj;
			int countPhoto = array.length();
			// countPhoto = Math.min(countPhoto, 10); // DEBUG
			if (countPhoto == 0) {
				return false;
			}
			response.setContentType("application/zip");
			response.setHeader("Content-Disposition", String.format("attachment; filename=\"album-%s.zip\"", groupBy));
			StringBuilder csvOut = new StringBuilder();
			csvOut.append(String.format(CSV_HEADER));
			OutputStream os = null;
			ZipOutputStream zos = null;
			try {
				zos = new ZipOutputStream(os = response.getOutputStream());
				for (int i = 0; i < countPhoto; i++) {
					try {
						JSONObject photo = array.getJSONObject(i);
						String id = photo.getJSONObject("_id").getString("$oid");
						String imageUrl = photo.getString("portal_image_url");
						if (!imageUrl.matches("^https?://.*")) {
							// image URL which is not imported from portal
							imageUrl = Config.ALBUM_ROOT + imageUrl;
						}
						String ext = imageUrl.substring(imageUrl.lastIndexOf(".")).toLowerCase();
						String subFolder = "";
						if ("season".equals(groupBy)) {
							subFolder = getSeason(photo, "不明");
						} else if ("date".equals(groupBy)) {
							subFolder = getDate(photo, "不明");
						} else if ("location".equals(groupBy)) {
							subFolder = getLocation(photo, "不明");
						}
						String imageFile = subFolder + "/" + id + ext;
						System.out.println(String.format("%d/%d\t%s", i + 1, countPhoto, imageUrl));
						if (saveImage(imageUrl, imageFile, apiKey, zos)) {
							csvOut.append(String.format(CSV_FORMAT, csvEscape(imageFile), csvEscape(getTitle(photo, "タイトルなし")),
									csvEscape(getDate(photo, "年代未整理")), csvEscape(getSeason(photo, "季節未整理")), csvEscape(getLocation(photo, "場所未整理"))));
						}
					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
				saveBytes(csvOut.toString().getBytes("Shift_JIS"), "index.csv", zos);
				saveBytes(README_TEXT.getBytes("Shift_JIS"), "説明.txt", zos);
				return true;
			} catch (Exception e) {
				e.printStackTrace();
			} finally {
				if (zos != null) {
					try {
						zos.close();
					} catch (IOException e) {
						e.printStackTrace();
					}
				}
				if (os != null) {
					try {
						os.close();
					} catch (IOException e) {
						e.printStackTrace();
					}
				}
			}
		}
		return false;
	}

	private static Object getJSON(String StrUrl, String apiKey) {
		// System.out.println(StrUrl);
		HttpURLConnection conn = null;
		InputStream is = null;
		try {
			URL url = new URL(StrUrl);
			conn = (HttpURLConnection) url.openConnection();
			conn.setConnectTimeout(60 * 1000);
			conn.setRequestMethod("GET");
			conn.setRequestProperty(HEADER_API_KEY, apiKey);
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

	private static boolean saveImage(String strUrl, String filePath, String apiKey, ZipOutputStream zos) throws IOException {
		HttpURLConnection conn = null;
		InputStream is = null;
		try {
			URL url = new URL(strUrl);
			conn = (HttpURLConnection) url.openConnection();
			conn.setConnectTimeout(60 * 1000);
			conn.setRequestMethod("GET");
			conn.setRequestProperty(HEADER_API_KEY, apiKey);
			conn.connect();
			is = conn.getInputStream();
			if (is != null) {
				zos.putNextEntry(new ZipEntry(filePath));
				byte[] buffer = new byte[16 * 1024];
				int len;
				while ((len = is.read(buffer, 0, buffer.length)) > 0) {
					zos.write(buffer, 0, len);
				}
				zos.closeEntry();
				return true;
			}
		} finally {
			if (conn != null) {
				conn.disconnect();
			}
			if (is != null) {
				try {
					is.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
			}
		}
		return false;
	}

	private static String getTitle(JSONObject photo, String defaultValue) {
		String value = getLastAnnotation(photo, "annotation_title", "title");
		if (value != null) {
			return value;
		}
		if (photo.containsKey("title")) {
			try {
				value = photo.getString("title");
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
		return value != null ? value : defaultValue;
	}

	private static String getSeason(JSONObject photo, String defaultValue) {
		String value = getLastAnnotation(photo, "annotation_season", "season");
		return value != null ? value : defaultValue;
	}

	private static String getDate(JSONObject photo, String defaultValue) {
		String value = getLastAnnotation(photo, "annotation_date", "date");
		return value != null ? value : defaultValue;
	}

	private static String getLocation(JSONObject photo, String defaultValue) {
		String value = getLastAnnotation(photo, "annotation_location", "address");
		return value != null ? value : defaultValue;
	}

	private static String csvEscape(String text) {
		return text.replace("\"", "\"\"");
	}

	private static String getLastAnnotation(JSONObject photo, String annotation, String key) {
		if (photo.containsKey(annotation)) {
			try {
				Object obj = photo.get(annotation);
				if (obj instanceof JSONArray) {
					JSONArray array = (JSONArray) obj;
					return array.getJSONObject(array.size() - 1).getString(key);
				}
			} catch (JSONException e) {
				e.printStackTrace();
			}
		}
		return null;
	}

	private static void saveBytes(byte[] bytes, String filePath, ZipOutputStream zos) throws IOException {
		zos.putNextEntry(new ZipEntry(filePath));
		zos.write(bytes);
		zos.closeEntry();
	}
}
