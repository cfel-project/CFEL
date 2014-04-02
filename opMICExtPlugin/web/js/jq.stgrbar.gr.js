(function($){
var stack = d3.layout.stack();
var __margin = {top:40, right:40, bottom:20, left:40};
var __fn_def_map = function(d){
	return d["event"];
};
var __fn_ev_load_filter = function(d){
	return d["event"].match(/^load/);
};
$.fn.stgr_bar_graph = function(odata, options){
	options = options || {};
	var margin = options.margin || __margin,
	width = (options.width || this.width()) - margin.left -margin.right,
	height = (options.height || this.height()) - margin.top - margin.bottom;

	//update
	this.empty();
	if(odata.length){
		var __extent = d3.extent(odata.map(function(d){return d["time"];}));
		var __time_min = __extent[0], __time_max = __extent[1];

		var __map = {};
		var n = 0;
		odata.forEach(function(d, i){
			var key = (options.fn_cluster || __fn_def_map)(d);
			if(!__map[key]){
				__map[key] = {};
				n++;
			}
			if(!options.fn_filter || options.fn_filter(d)){
				__map[key]["" + Math.floor((d["time"] - __time_min) / 86400)] = (__map[key]["" + Math.floor((d["time"] - __time_min) / 86400)] || 0) + (1 * d["count"]);
			}
		});
		var m = (__time_max - __time_min) / 86400 + 1;
		var data = [];
		var label_legend = [];
		for(var k in __map){
			label_legend.push(k);
			data.push(d3.range(m).map(function(d,i){
				return {x:i, y: __map[k]["" + i] || 0};
			}));
		}

		var layers = stack(data),
		y_group_max = d3.max(layers, function(l){return d3.max(l, function(d){return d.y;});}),
		y_stack_max = d3.max(layers, function(l){return d3.max(l, function(d){return d.y0 + d.y;});});

		var x = d3.scale.ordinal()
			.domain(d3.range(m))
			.rangeRoundBands([0, width], .08);
		var y = d3.scale.linear()
			.domain([0, y_stack_max])
			.range([height, 0]);

		var color = d3.scale.category20();//d3.scale.linear()
	//		.domain([0, n - 1])
	//		.range(options.color_range || ["#aad", "#556"]);

		var xAxis = d3.svg.axis()
			.scale(x)
			.tickSize(3)
			.tickPadding(6)
			.tickFormat(function (d){return 0 == d%5 ? moment(__time_min * 1000 + 86400000 * d).format("M/D") : "";})
			.orient("bottom");
		var yAxis = d3.svg.axis()
			.scale(y)
			.orient("left")
			.tickFormat(d3.format(".2s"));

		var svg = d3.select(this[0]).append("svg")
			.attr("width", width + margin.left + margin.right)
			.attr("height", height + margin.top + margin.bottom)
			.append("g")
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		var layer = svg.selectAll(".layer")
			.data(layers)
			.enter().append("g")
			.attr("class", "layer")
			.style("fill", function(d,i){return color(i);});

		var rect = layer.selectAll("rect")
			.data(function(d){return d;})
			.enter().append("rect")
			.attr("x", function(d){return x(d.x);})
			.attr("y", height)
			.attr("width", x.rangeBand())
			.attr("height", 0);

		rect.transition()
			.delay(function(d,i){return i * 10;})
			.attr("y", function(d){return y(d.y0 + d.y);})
			.attr("height", function(d){return y(d.y0) - y(d.y0 + d.y);});

		var g_x_axis = svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(0," + height + ")")
			.call(xAxis);
		var g_y_axis = svg.append("g")
			.attr("class", "y axis")
			.call(yAxis)
			.append("text")
			.attr("transform", "rotate(-90)")
			.attr("y", 6)
			.attr("dy", ".71em")
			.style("text-anchor", "end")
			.text("Activities");

		function change(){
			if(this.value === "grouped") transitionGrouped();
			else transitionStacked();
		}

		d3.selectAll("input").on("change", change);

		function transitionGrouped(){
			svg.selectAll("g .y.axis")
				.transition().delay(500)
				.call(yAxis.scale(y.domain([0, y_group_max])));
			rect.transition()
				.duration(500)
				.delay(function(d,i){return 10 * 1;})
				.attr("x", function(d,i,j){return x(d.x) + x.rangeBand() / n * j;})
				.attr("width", x.rangeBand() / n)
				.transition()
				.attr("y", function(d){return y(d.y);})
				.attr("height", function(d){return height - y(d.y);});
		}

		function transitionStacked(){
			svg.selectAll("g .y.axis")
				.transition().delay(500)
				.call(yAxis.scale(y.domain([0, y_stack_max])));
			rect.transition()
				.duration(500)
				.delay(function(d,i){return 10 * i;})
				.attr("y", function(d){return y(d.y0 + d.y);})
				.attr("height", function(d){ return y(d.y0) - y(d.y0 + d.y);})
				.transition()
				.attr("x", function(d){return x(d.x);})
				.attr("width", x.rangeBand());
		}

		var legend = svg.selectAll(".legend")
			.data(d3.range(n))//color.domain().slice().reverse())
			.enter().append("g")
			.attr("class", "legend")
			.attr("transform", function(d,i){return "translate(0," + i * 20 + ")";});
		legend.append("rect")
			.attr("x", width + 8)
			.attr("width", 18)
			.attr("height", 18)
			.style("fill", color);
		legend.append("text")
			.attr("x", width - 4)
			.attr("y", 9)
			.attr("dy", ".35em")
			.style("text-anchor", "end")
			.text(function(d){return label_legend[d];});
	}
	return this;
};
})(jQuery);
