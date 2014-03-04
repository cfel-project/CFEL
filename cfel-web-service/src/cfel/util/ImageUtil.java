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

import java.awt.Graphics2D;
import java.awt.Image;
import java.awt.image.BufferedImage;
import java.io.IOException;
import java.net.URL;

import javax.imageio.ImageIO;

public class ImageUtil {

	private static final int THUMBNAIL_WIDTH = 200, THUMBNAIL_HEIGHT = 200;
	private static final int CARD_WIDTH = 400, CARD_HEIGHT = 300;
	private static final int MARKER_CONTENTWIDTH = 120, MARKER_CONTENTHEIGHT = 80;

	public static BufferedImage[] convertImage(URL url) {
		try {
			BufferedImage src = ImageIO.read(url);
			BufferedImage thumbnail = getScaledImage(src, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
			BufferedImage markerContent = getScaledImage(src, MARKER_CONTENTWIDTH, MARKER_CONTENTHEIGHT);
			BufferedImage card = getScaledImage(src, CARD_WIDTH, CARD_HEIGHT);
			src.flush();

			BufferedImage marker = ImageIO.read(ImageUtil.class.getResourceAsStream("arr.png"));
			int d = (marker.getWidth() - MARKER_CONTENTWIDTH) / 2;
			Graphics2D g = marker.createGraphics();
			g.drawImage(markerContent, d, d, MARKER_CONTENTWIDTH, MARKER_CONTENTHEIGHT, null);
			g.dispose();
			markerContent.flush();
			return new BufferedImage[] { thumbnail, marker, card };
		} catch (IOException e) {
			e.printStackTrace();
		}
		return null;
	}

	private static BufferedImage getScaledImage(BufferedImage src, int maxWidth, int maxHeight) {
		int srcWidth = src.getWidth();
		int srcHeight = src.getHeight();
		double srcRatio = (double) srcWidth / srcHeight;
		double maxRatio = (double) maxWidth / maxHeight;
		int targetWidth;
		int targetHeight;
		int targetX;
		int targetY;
		if (maxRatio > srcRatio) {
			targetHeight = maxHeight;
			targetY = 0;
			targetWidth = (int) (srcRatio * targetHeight);
			targetX = (maxWidth - targetWidth) / 2;
		} else {
			targetWidth = maxWidth;
			targetX = 0;
			targetHeight = (int) ((double) targetWidth / srcRatio);
			targetY = (maxHeight - targetHeight) / 2;
		}

		BufferedImage image = new BufferedImage(maxWidth, maxHeight, BufferedImage.TYPE_INT_ARGB);
		Graphics2D g = image.createGraphics();
		g.drawImage(src.getScaledInstance(targetWidth, targetHeight, Image.SCALE_AREA_AVERAGING), targetX, targetY, targetWidth, targetHeight, null);
		g.dispose();
		return image;
	}
}
