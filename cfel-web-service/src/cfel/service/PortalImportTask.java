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

import java.awt.image.BufferedImage;
import java.io.IOException;
import java.io.OutputStream;
import java.net.MalformedURLException;
import java.net.URL;
import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Locale;
import java.util.Set;
import java.util.TimerTask;

import javax.imageio.ImageIO;
import javax.servlet.ServletContext;

import org.apache.wink.json4j.JSONArray;
import org.apache.wink.json4j.JSONException;
import org.apache.wink.json4j.JSONObject;

import cfel.util.ImageUtil;
import cfel.util.WSClientUtil;

import com.mongodb.BasicDBObject;
import com.mongodb.DBCollection;
import com.mongodb.DBObject;

public class PortalImportTask extends TimerTask {
	public static final String ACTIVITY_SEARCH_URL = Config.PORTAL_URL + "/api.php/activity/search.json";
	public static final String ELMEMBER_SEARCH_API = Config.PORTAL_URL + "/api.php/elmember/search";
	public static final String FILE_URL = "/file/";
	public static final DateFormat FORMAT_DATE_ISO = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ssX");
	public static final DateFormat FORMAT_DATE = new SimpleDateFormat("EEE, dd MMM yyyy HH:mm:ss Z", Locale.ENGLISH);
	private static final String[] IMAGE_TYPES = new String[] { "thumbnail", "marker", "card" };

	private DatabaseServive mDS;
	private DBCollection mPhotoCollection;

	private final ServletContext mServletContext;

	public PortalImportTask(ServletContext servletContext) {
		super();
		this.mServletContext = servletContext;
		try {
			mDS = DatabaseServive.getInstance();
			mPhotoCollection = mDS.getCollection("photo");
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	@Override
	public void run() {
		System.out.println("PortalImportTask started at " + new Date().toString());
		if (mPhotoCollection == null) {
			System.err.println("mongod is not running");
			return;
		}
		PortalActivity activity = new PortalActivity();
		Set<String> imageUrlSet = new HashSet<String>();
		for (int temp = 0; activity.hasNext() && temp < 999; temp++) {// while (activity.hasNext()) {
			JSONArray dataList = activity.getNext();
			if (dataList == null) {
				imageUrlSet = null;
				System.err.println("Error loading activities");
				break;
			}
			for (int i = 0; i < dataList.length(); i++) {
				try {
					JSONObject obj = dataList.getJSONObject(i);
					String imageUrl = getImageURL(obj);
					if (imageUrl != null) {
						imageUrlSet.add(imageUrl);
						importActivity(obj, imageUrl);
					}
				} catch (JSONException e) {
					e.printStackTrace();
				}
			}
		}
		fixThumbnailImages();
		if (imageUrlSet != null) {
			fixActivities(imageUrlSet);
		}
		System.out.println("PortalImportTask finished at " + new Date().toString());
	}

	private void importActivity(JSONObject activity, String portal_image_url) {
		// System.out.println(activity.toString());
		try {
			JSONObject member = activity.getJSONObject("member");
			if (member.containsKey("id")) {
				String title = activity.getString("body").replace(activity.getString("image_large_url"), "").trim();
				DBObject photo = new BasicDBObject();
				photo.put("portal_image_url", portal_image_url);
				if (mPhotoCollection.count(photo) > 0) {
					System.err.println(portal_image_url + " already exists");
					return;
				} else {
					System.out.println("Inserting " + portal_image_url);
				}
				String imageSourceUrl = Config.PORTAL_URL.startsWith("http:") ? portal_image_url.replace("https://", "http://") : portal_image_url;
				BufferedImage[] images = ImageUtil.convertImage(new URL(imageSourceUrl));
				if (images == null) {
					System.err.println(portal_image_url + " corrupted");
					return;
				}
				saveThumbnailImages(photo, images, "/" + getFileId(portal_image_url) + ".png");
				photo.put("uploader_user_id", member.getString("id"));
				photo.put("title", title);
				Object exifObj = getExifData(portal_image_url);
				Date date = null;
				if (exifObj instanceof JSONObject) {
					JSONObject exif = (JSONObject) exifObj;
					if (exif.containsKey("date")) {
						try {
							date = FORMAT_DATE_ISO.parse(exif.getString("date"));
						} catch (ParseException e) {
							e.printStackTrace();
						}
					}
					if (exif.containsKey("location")) {
						photo.put("exif_location", exif.getJSONArray("location"));
					}
				}
				if (date == null && activity.containsKey("created_at")) {
					try {
						date = FORMAT_DATE.parse(activity.getString("created_at"));
					} catch (ParseException e) {
						e.printStackTrace();
					}
				}
				if (date != null) {
					photo.put("exif_date", date);

				}
				mPhotoCollection.insert(photo);
				// System.out.println(photo);
				// System.out.println();
			}
		} catch (JSONException | MalformedURLException e) {
			e.printStackTrace();
		}
	}

	private static String getImageURL(JSONObject activity) {
		String result = null;
		try {
			Object image_url = activity.get("image_large_url");
			if (image_url instanceof String && !image_url.toString().endsWith("/no_image.gif") && activity.containsKey("member")) {
				result = (String) image_url;
				if (!result.matches("^https?://.*")) {
					result = Config.PORTAL_ROOT + result;
				}
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
		return result;
	}

	private static final String EXIF_SEARCH_URL = Config.PORTAL_URL + "/api.php/imgexf/search?apiKey=" + Config.API_KEY;

	private JSONObject getExifData(String image_url) {
		Object obj = WSClientUtil.getJSON(EXIF_SEARCH_URL + "&name=" + getFileId(image_url));
		return (obj instanceof JSONObject) ? (JSONObject) obj : null;
	}

	private String getFileId(String image_url) {
		String[] part = image_url.split("/");
		String[] subPart = part[part.length - 1].split("\\.");
		return subPart[0];
	}

	private void saveThumbnailImages(DBObject photo, BufferedImage[] images, String suffix) {
		for (int i = 0; i < IMAGE_TYPES.length; i++) {
			String fileName = IMAGE_TYPES[i] + suffix;
			OutputStream os = mDS.getFileOutputStream(fileName, "image/png");
			try {
				if (os != null) {
					ImageIO.write(images[i], "png", os);
					photo.put(IMAGE_TYPES[i] + "_image_url", FILE_URL + fileName);
				}
			} catch (IOException e) {
				e.printStackTrace();
			} finally {
				if (os != null) {
					try {
						os.close();
					} catch (IOException e) {
						e.printStackTrace();
					}
				}
				images[i].flush();
			}
		}
	}

	private void fixThumbnailImages() {
		DBObject query = new BasicDBObject("card_image_url", new BasicDBObject("$exists", false));
		for (Iterator<DBObject> it = mPhotoCollection.find(query); it.hasNext();) {
			DBObject photo = it.next();
			if (!photo.containsField("portal_image_url")) {
				System.err.println("No portal_image_url in " + photo);
				continue;
			}
			String portal_image_url = photo.get("portal_image_url").toString();
			if (!portal_image_url.matches("^https?://.*")) {
				portal_image_url = Config.ALBUM_ROOT + portal_image_url;
			}
			try {
				URL url = new URL(portal_image_url);
				System.err.println("Missing thumbnail images for " + url);
				BufferedImage[] images = ImageUtil.convertImage(url);
				if (images != null) {
					DBObject updates = new BasicDBObject();
					saveThumbnailImages(updates, images, url.getPath().replace(".", "_") + ".png");
					mPhotoCollection.update(photo, new BasicDBObject("$set", updates));
				} else {
					System.err.println(url + " corrupted");
				}
			} catch (MalformedURLException e) {
				System.err.println("Unknown image URL" + portal_image_url);
				continue;
			}
		}
	}

	private void fixActivities(Set<String> imageSet) {
		DBObject query = new BasicDBObject("$and", new DBObject[] { new BasicDBObject("portal_image_url", new BasicDBObject("$exists", true)),
				new BasicDBObject("uploader_user_id", new BasicDBObject("$exists", true)) });
		for (Iterator<DBObject> it = mPhotoCollection.find(query); it.hasNext();) {
			DBObject photo = it.next();
			boolean temporary_removed = !imageSet.contains(photo.get("portal_image_url").toString());
			boolean removed = Boolean.TRUE.equals(photo.get("permanently_removed")) || temporary_removed;
			if (temporary_removed != Boolean.TRUE.equals(photo.get("temporary_removed"))) {
				mPhotoCollection.update(photo, new BasicDBObject("$set", new BasicDBObject("temporary_removed", temporary_removed)));
			}
			if (removed != Boolean.TRUE.equals(photo.get("removed"))) {
				mPhotoCollection.update(photo, new BasicDBObject("$set", new BasicDBObject("removed", removed)));
				System.out.println("removed:" + removed + " " + photo);
			}
		}
	}
}
