(function($){
$.fn.show_loading_screen = function(){
	var scr = $(this).find("#loading_screen");
	if(scr.length == 0){
		scr = $("<div id='loading_screen' style='display:none;position:fixed;top:0;left:0;width:100%;height:100%; background:rgba(255,255,255,.8)'><h1 class='loader' style='text-align:center;margin-top:40%;'>読み込み中...</h1></div>").appendTo($(this));
	}
	scr.show(100);
};
$.fn.hide_loading_screen = function(){
	$(this).find("#loading_screen").hide();
};
})(jQuery);
