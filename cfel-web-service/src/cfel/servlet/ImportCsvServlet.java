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

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.Date;
import java.util.List;

import javax.servlet.ServletException;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;

import cfel.service.DatabaseServive;

import com.mongodb.BasicDBObject;
import com.mongodb.DBCollection;
import com.mongodb.DBObject;

/**
 * Servlet implementation class ImportCsvServlet
 */
@WebServlet("/import_csv/photo/classes")
public class ImportCsvServlet extends HttpServlet {
	private static final long serialVersionUID = 1L;

	private DatabaseServive mDS = DatabaseServive.getInstance();

	/**
	 * @see HttpServlet#HttpServlet()
	 */
	public ImportCsvServlet() {
		super();
		// TODO Auto-generated constructor stub
	}

	/**
	 * @see HttpServlet#doGet(HttpServletRequest request, HttpServletResponse
	 *      response)
	 */
	protected void doGet(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		System.err.println("GET is not valid for ImportCsvServlet");
	}

	/**
	 * @see HttpServlet#doPost(HttpServletRequest request, HttpServletResponse
	 *      response)
	 */
	protected void doPost(HttpServletRequest request, HttpServletResponse response) throws ServletException, IOException {
		DBCollection collection = mDS.getCollection("photo");
		if (collection == null) {
			response.sendError(404, "No photo collection");
			return;
		}
		InputStream is = null;
		try {
			is = request.getInputStream();
			BufferedReader br = new BufferedReader(new InputStreamReader(is));
			String line = br.readLine(); // Ignore header
			while ((line = br.readLine()) != null) {
				String[] values = getStrings(line);
				if (values.length == 2) {
					String portal_image_url = values[0];
					String class_id = values[1];
					System.out.println("url=" + portal_image_url + ", season=" + class_id);
					DBObject query = new BasicDBObject("portal_image_url", portal_image_url);
					DBObject photo = collection.findOne(query);
					if (photo == null) {
						continue;
					}
					Object guessObj = photo.get("guess");
					if (guessObj instanceof List<?>) {
						System.out.println(guessObj);
						List<?> guess = (List<?>) guessObj;
						Object lastGuess = guess.get(guess.size() - 1);
						if (lastGuess instanceof DBObject && class_id.equals(((DBObject) lastGuess).get("class_id"))) {
							continue;
						}
					}
					DBObject newGuess = new BasicDBObject();
					newGuess.put("updated", new Date());
					newGuess.put("class_id", class_id);
					DBObject update = new BasicDBObject("$push", new BasicDBObject("guess", newGuess));
					collection.update(photo, update, true, false);
				} else {
					System.err.println("Unknown data: " + line);
				}
			}

		} finally {
			if (is != null) {
				is.close();
			}
		}
		response.setStatus(200);
	}

	private String[] getStrings(String line) {
		String[] values = line.split(",");
		for (int i = 0; i < values.length; i++) {
			String text = values[i].trim();
			if (text.startsWith("\"") && text.endsWith("\"")) {
				values[i] = text.substring(1, text.length() - 1);
			}
		}
		return values;
	}

}
