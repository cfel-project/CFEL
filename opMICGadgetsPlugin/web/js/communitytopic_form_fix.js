(function($){
$.fn.dsl_communitytopic_form_fix = function(options){
	var form = this.find("form");
	if(options.action_src){
		form.attr("action", form.attr("action").replace(options.action_src, options.action_dst || ""));
	}
	form.find("strong").filter(function(){
		return "*"==$(this).text();
	}).css("color", "#b7282e");
	form.find("input[type='submit']")
	 .addClass("btn-primary")
	 .css({
		padding: ".3em 2em"
	});
	this.find("div.parts").addClass("row");
	this.find("div.parts>.partsHeading").removeClass("partsHeading").addClass("gadget_header").each(function(){
		$(this).html($(this).text());
	});
};
})(jQuery);
