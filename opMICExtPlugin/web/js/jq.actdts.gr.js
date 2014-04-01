(function($){
var __margin = {top: 5, right: 5, bottom: 20, left: 30};

$.fn.actdts_ar_graph = function(_dt, options){
	options = options || {};
	var margin = $.extend({}, __margin, options.margin || {});
	width = (options.width || this.width()) - margin.left -margin.right,
	height = (options.height || this.height()) - margin.top - margin.bottom,
	x = d3.time.scale().range([0, width]),
	y = d3.scale.linear().range([height,0]);

	this.empty();

	var tm_map = {};
	_dt.forEach(function(d){
		if(!tm_map["" + d.time]){
			tm_map["" + d.time] = 0;
		}
		tm_map["" + d.time] += (1 * d.count);
	});
	var tm_ext = d3.extent(_dt.map(function(d){return 1 * d.time;}).concat((options.dt_range || []).map(function(d){return d.getTime() / 1000;})));
	var data = d3.range(tm_ext[0], tm_ext[1] + 1, 86400).map(function(d){
		return {time: new Date(1000 * d), count: tm_map["" + d] || 0};
	});

	var area  = d3.svg.area()
		.interpolate("monotone")
		.x(function(d){return x(d.time);})
		.y0(height)
		.y1(function(d){ return y(1 * d.count);});

	var svg = d3.select(this[0]).append("svg")
		.attr("width", width + margin.left + margin.right)
		.attr("height", height + margin.top + margin.bottom);

	svg.append("defs").append("clipPath")
		.attr("id", "clip")
		.append("rect")
		.attr("width", width)
		.attr("height", height);

	var focus = svg.append("g")
		.attr("class", "focus")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");


	//apply data
	x.domain(d3.extent(data.map(function(d){return d.time;})));
	y.domain([0, d3.max(data.map(function(d){return 1 * d.count;}))]);

	var xAxis = d3.svg.axis()
		.scale(x)
		.tickSize(3)
		.tickFormat(function (d){return moment(d).format("M/D");})
		.orient("bottom"),
		yAxis = d3.svg.axis().ticks((height-margin.bottom - margin.top )/5 || 1).scale(y).orient("left");

	if(options.tickSize){
		[xAxis, yAxis].forEach(function(d){
			d.tickSize(options.tickSize);
		});
	}

	focus.append("path")
		.datum(data)
		.attr("class", "area")
		.attr("d", area);
	focus.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);
	if(!options.no_yaxis){
		focus.append("g")
			.attr("class", "y axis")
			.call(yAxis);
	}

	if(options.on_brush){
		var brush = d3.svg.brush()
			.x(x)
			.on("brush", function(){
				options.on_brush(brush.extent());
			});
		focus.append("g")
			.attr("class", "x brush")
			.call(brush)
			.selectAll("rect")
			.attr("y" - 6)
			.attr("height", height + 7);
	}

	return this;
};
})(jQuery);
