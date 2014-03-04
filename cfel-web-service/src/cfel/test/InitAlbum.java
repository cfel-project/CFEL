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

package cfel.test;

import java.io.File;
import java.io.FileFilter;
import java.io.FileInputStream;
import java.io.IOException;

import com.mongodb.DB;
import com.mongodb.DBObject;
import com.mongodb.MongoClient;
import com.mongodb.gridfs.GridFS;
import com.mongodb.gridfs.GridFSInputFile;

public class InitAlbum {

	private static DB db = null;
	private static GridFS fs = null;
	private static final File IMAGE_DIR = new File("/xampp/htdocs/album/img");
	private static final String IMAGE_DIR_PATH = IMAGE_DIR.toURI().getPath();

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		try {
			MongoClient mongoClient = new MongoClient("localhost", 27017);
			db = mongoClient.getDB("albumdb");
			fs = new GridFS(db);
			fs.remove((DBObject) null);
		} catch (Exception e) {
			e.printStackTrace();
			return;
		}
		System.out.println(IMAGE_DIR_PATH);
		addImages(IMAGE_DIR);
	}

	private static void addImages(File dir) {
		dir.listFiles(new FileFilter() {
			@Override
			public boolean accept(File file) {
				if (file.isDirectory()) {
					addImages(file);
				} else {
					String fileName = file.getName().toLowerCase();
					if (fileName.endsWith(".jpg") || fileName.endsWith(".png")) {
						String path = "img/" + file.toURI().getPath().substring(IMAGE_DIR_PATH.length());
						System.out.println(path);
						try {
							GridFSInputFile gfFile = fs.createFile(new FileInputStream(file), path, true);
							gfFile.save();
						} catch (IOException e) {
							e.printStackTrace();
						}
					}
				}
				return false;
			}
		});
	}

}
