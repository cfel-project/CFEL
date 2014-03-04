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

import java.io.IOException;
import java.io.OutputStream;
import java.net.UnknownHostException;
import java.util.Iterator;
import java.util.Set;

import com.mongodb.BasicDBObject;
import com.mongodb.DB;
import com.mongodb.DBCollection;
import com.mongodb.DBObject;
import com.mongodb.MongoClient;
import com.mongodb.gridfs.GridFS;
import com.mongodb.gridfs.GridFSDBFile;
import com.mongodb.gridfs.GridFSInputFile;
import com.mongodb.util.JSON;

public class Tester {

	private static DB db = null;
	private static GridFS fs = null;

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		try {
			MongoClient mongoClient = new MongoClient("localhost", 27017);
			db = mongoClient.getDB("mydb");
			fs = new GridFS(db);
		} catch (UnknownHostException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		Set<String> colls = db.getCollectionNames();
		for (String s : colls) {
			System.out.println(s);
			DBCollection col = db.getCollection(s);
			for (Iterator<DBObject> it = col.find(); it.hasNext();) {
				DBObject obj = it.next();
				System.out.println(obj.toString());
			}
		}
		System.out.println("-------- mydb --------");
		DBCollection col = db.getCollection("mydb");
		col.remove(new BasicDBObject());
//		fs.remove(new BasicDBObject());
		for (int i = 1; i <= 10; i++) {
			DBObject o1 = (DBObject) JSON.parse("{a:" + i + "}");
			col.insert(o1);
		}

		fs.remove("test.bin");
		fs.remove("test.bin");
		try {
			GridFSInputFile db_file = fs.createFile("test.bin");
			OutputStream os = db_file.getOutputStream();

			final int len = 4096;
			byte data[] = new byte[] { 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 };
			os.write(data);
			os.flush();
			os.close();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		System.out.println(col.count() + " documents");
		for (Iterator<DBObject> it = col.find(); it.hasNext();) {
			DBObject obj = it.next();
			System.out.println(obj.toString());
		}
		for (GridFSDBFile file : fs.find(new BasicDBObject())) {
			System.out.println(file.toString());
		}
	}

}
