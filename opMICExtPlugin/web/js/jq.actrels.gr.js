(function($){
var __margin = {top:0, right:0, bottom:0, left:0};

$.fn.actrel_fr_graph = function(data, options){
	options = options || {};
	var margin = options.margin || __margin,
	container = $(this),
	width = (options.width || container.width()) - margin.left -margin.right,
	height = (options.height || container.height()) - margin.top - margin.bottom,
	__r = (Math.min(width, height))/20.0;
	

	//update
	container.empty();

	var __m_id_map = {};
	data.nodes.forEach(function(d,i){
		__m_id_map[d.id] = i;
	});
	var __links = data.links.filter(function(d){
			return "undedined" != typeof(__m_id_map[d.src]) && "undefined" != typeof(__m_id_map[d.trg]);
		}).map(function(d){
			return {
				"source": __m_id_map[d.src],
				"target": __m_id_map[d.trg],
				"value": d.count
			};
		});

	var svg = d3.select(container[0]).append("svg")
		.attr("width", width)
		.attr("height", height);

	var defs = svg.append('defs');
	var gradient = defs.append('radialGradient')
		.attr('id', 'fadient');
	gradient.append('stop')
		.attr('offset', '80%')
		.attr('stop-color', 'white')
		.attr('stop-opacity', 0.3);
	gradient.append('stop')
		.attr('offset', '100%')
		.attr('stop-color', 'white')
		.attr('stop-opacity', 1);
	var mask = defs.append('mask')
		.attr('id', 'mask')
		.attr('maskContentUnits', 'objectBoundingBox')
		.append('circle')
		.attr('fill', 'url(#fadient)')
		.attr('cx', .5)
		.attr('cy', .5)
		.attr('r', .5);

	var force = d3.layout.force()
		.gravity(3.5 / __r)
		.distance(4.5 * __r)
		.charge(-13.0 * __r)
//		.friction(.7)
		.size([width, height]);

	var __get_scale = function(d){
		return .5 + .5 * Math.log(1 + Math.sqrt(d.count));
	};

	force.nodes(data.nodes)
		.links(__links)
		.linkDistance(function(d){
			return (__r * (__get_scale(d.source) + __get_scale(d.target))) * 1.1 +  (__r / (2.0 * d.value));
		})
//		.chargeDistance(function(d){
//			return -1 * (__r * (__get_scale(d.source) + __get_scale(d.target))) +  (__r / (2.0 * d.value));
//		})
		.linkStrength(function(d){
			return .6;// + (.2 * d.value);
		})
		.start();

	var link = svg.selectAll(".link")
		.data(__links)
		.enter().append("line")
		.attr("class", "link");

	var node = svg.selectAll(".node")
		.data(data.nodes)
		.enter().append("g")
		.attr("class", "node")
		.call(force.drag);

	node.append("circle")
		.attr("r", function(d){ return __r * __get_scale(d);})
		.attr('mask', 'url(#mask)')
		.attr("fill", "#bce2e8");//"#bbc8e6");

	node.append("image")
		.attr("xlink:href", function(d){ return d.image;})
		.attr("x", function(d){return -.565 * __r * __get_scale(d);})
		.attr("y", function(d){return -.565 * __r * __get_scale(d);})
		.attr("width", function(d){return 1.13 * __r * __get_scale(d);})
		.attr("height", function(d){return 1.13 * __r * __get_scale(d);})
		.on("click", function(d){
			window.location.href=d.prof_url;
		});

	node.append("text")
		.attr("y", function(d){return .9 * __r * __get_scale(d);})
		.attr("text-anchor", "middle")
		.attr("font-size", .5 * __r)
		.text(function(d){return d.name;});

	force.on("tick", function(){
		link.attr("x1", function(d){return d.source.x;})
			.attr("y1", function(d){return d.source.y;})
			.attr("x2", function(d){return d.target.x;})
			.attr("y2", function(d){return d.target.y;});
		node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	});
	return container;
};
})(jQuery);
