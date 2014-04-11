/*******************************************************************************
 * Copyright (c) 2014 IBM Corporation and Others All rights reserved. This
 * program and the accompanying materials are made available under the terms of
 * the Eclipse Public License v1.0 which accompanies this distribution, and is
 * available at http://www.eclipse.org/legal/epl-v10.html
 * 
 * Contributors: IBM Corporation - initial API and implementation
 ******************************************************************************/
function openPrintView(tab, pageTitle) {
	var win = window.open("");
	var items = tab.getElementsByTagName("li");
	var html = '<html>';
	html += '<head>';
	html += '<title></title>';
	html += '</head>';
	html += '<body>';
	for (var i = 0; i < items.length; i++) {
		if (i % 4 == 0) {
			if (i + 4 > items.length - 1) {
				html += '<table>';
			} else {
				html += '<table style="page-break-after:always;">';
			}
			html += '<tr><td></td><td style="text-align:right; padding-right:0px;">' + pageTitle + ", " + (Math.floor(i/4)+1) + '/' + (Math.floor(items.length/4)+1) +'</td></tr>'			
		}
		if (i % 2 == 0) {
			html += '<tr>';
		}
		html += '<td width="50%">';
		var li = $(items.item(i));
		html += '<div style="padding:10px;">';
		html += '<img width="100%" src="' + li.find("a").attr("href") + '"/>';
		var title = li.find("#title").text();
		if ("この赤いリンクをタッチしてタイトルをつけてください" != title) {
			if (title.length > 140) {
				title = title.substr(0, 140) + "...";
			}
			html += '<div style="text-align:center; margin-top:10px; font-size:100%;">'
					+ title + '</div>';
		} else {
			html += '<div style="text-align:center; margin-top:10px; font-size:100%;">タイトルが未入力の画像です</div>';
		}
		html += '</div>';
		html += '</td>';
		if (i % 2 == 1) {
			html += '</tr>';
		}
		if (i % 4 == 3) {
			html += '</table>';
			// html += '<hr/>'; // for debug
		}
	}
	if (items.length % 4 != 0) {
		if (items.length % 2 != 0) {
			html += '<td>';
			html += '</td>';
			html += '</tr>';
		}
		html += '</table>';
	}
	html += '</body>';
	html += '</html>';
	win.document.write(html);
}