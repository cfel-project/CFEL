(function($){
$.fn.dsl_src_protocol_fix = function(options){
	return $(this).each(function(){
		var rexp = /((?:http|https):\/\/[^\s^<]+)/g;
		if (rexp.test($(this).attr("src"))) {
			$(this).attr("src",$(this).attr("src").replace(/^(http|https):\/\//,"//").replace(/#t=/,"?start="));
			$(this).replaceWith(this.outerHTML);
		}
    });
};
$.fn.dsl_url_text_replace = function(options){
	return $(this).each(function(){
		var rexp = /((?:http|https):\/\/[^\s^<]+)/g;
		if(rexp.test(this.innerHTML)){
			this.innerHTML = this.innerHTML.replace(rexp, "<a href='$1'>$1</a>");
		}
	});
};
$.fn.dsl_youtube_link_replace = function(options){
	options = options || {};
	return $(this).each(function(){
		var ryoutube = /(?:http:\/\/|https:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?!embed)(?:watch\?v=)?(.{11})/g;
		if(ryoutube.test($(this).attr("href"))){
			var width = options.width || $(this).innerWidth();
			var height = options.height || Math.floor(width * 3 / 4);
			
			$(this).replaceWith("<iframe width=\"" + width + "\" height=\"" + height + "\" src=\"" + $(this).attr("href").replace(ryoutube, "//www.youtube.com/embed/$1").replace(/#t=/,"?start=") + "\" frameborder=\"0\" allowfullscreen></iframe>");
		}
	});
};
})(jQuery);