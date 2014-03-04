(function($){

$.fn.dsl_event_notify = function(params){
	this.click(function(evt){
		evt.preventDefault();
		if(window.confirm('コミュニティ全員に最新のイベント情報のメールを送付します。よろしいですか？')){
			var sendButton = $(this);
			sendButton.addClass("dslLoading").attr("disabled", true)

			$.ajax({
				type:"POST",
				url: openpne.apiBase + 'dslevent/reportEvent?apiKey=' + openpne.apiKey,
				data: params.data,
				dataType: "json",
				success: function(data){
					sendButton.attr("disabled", false).removeClass("dslLoading");
					if(params.success)
						params.success(data);
				},
				error: function(xhr, msg, err){
					sendButton.attr("disabled", false).removeClass("dslLoading");
					if(params.error)
						params.error(msg, err);
				}
			});
		}
	});
	return this;
};
})(jQuery);