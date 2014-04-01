(function($){
var __margin = {top:0, right:0, bottom:0, left:0};

$.fn.actrel_fr_graph = function(options){
	options = options || {};
	var margin = options.margin || __margin,
	width = (options.width || this.width()) - margin.left -margin.right,
	height = (options.height || this.height()) - margin.top - margin.bottom,
	__r = (Math.min(width, height))/20.0;

	//update
	this.empty();

	function __remap_links(data){
		var __m_id_map = {};
		data.nodes.forEach(function(d,i){
			__m_id_map[d.id] = i;
		});
		return data.links.filter(function(d){
				return "undedined" != typeof(__m_id_map[d.src]) && "undefined" != typeof(__m_id_map[d.trg]);
			}).map(function(d){
				return {
					"source": __m_id_map[d.src],
					"target": __m_id_map[d.trg],
					"value": d.count
				};
			});
	}

	var svg = d3.select(this[0]).append("svg")
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
		.gravity(3.6 / __r)
		.distance(4.5 * __r)
		.charge(-11.0 * __r)
//		.friction(.7)
		.size([width, height]);

	var __get_scale = function(d){
		return .5 + .5 * Math.log(1 + Math.sqrt(d.count));
	};

	var link = svg.selectAll(".link");
	var node = svg.selectAll(".node");
	force.on("tick", function(){
		(link || svg.selectAll(".link")).attr("x1", function(d){return d.source.x;})
			.attr("y1", function(d){return d.source.y;})
			.attr("x2", function(d){return d.target.x;})
			.attr("y2", function(d){return d.target.y;});
		(node || svg.selectAll(".node")).attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
	});

	this.actrel_fr_graph_update = function(data){
		var __links = __remap_links(data);
		force.nodes(data.nodes)
			.links(__links)
			.linkDistance(function(d){
				return (__r * (__get_scale(d.source) + __get_scale(d.target))) +  (__r / (3 * d.value));
			})
	//		.chargeDistance(function(d){
	//			return -1 * (__r * (__get_scale(d.source) + __get_scale(d.target))) +  (__r / (2.0 * d.value));
	//		})
			.linkStrength(function(d){
				return .04;// + (.2 * d.value);
			});

		link = link.data(__links);
		link.exit().remove();
		var l = link.enter().append("line")
			.attr("class", "link");

		node = node.data(data.nodes, function(d){return d.id;});
		node.exit().remove();
		var g = node.enter().append("g")
			.attr("class", "node")
			.call(force.drag);


		var circle = g.append("circle")
			.attr('mask', 'url(#mask)')
			.attr("fill", "#bce2e8");//"#bbc8e6");

		var image = g.append("image")
			.attr("xlink:href", function(d){ return d.image;})
			.on("click", function(d){
				window.location.href=d.prof_url;
			});

		var text = g.append("text")
			.attr("text-anchor", "middle")
			.text(function(d){return d.name;});

		//update representations
		svg.selectAll(".node circle").data(data.nodes, function(d){return d.id;})
			.attr("r", function(d){
				return __r * __get_scale(d);
			});
		svg.selectAll(".node image").data(data.nodes, function(d){return d.id;})
			.attr("x", function(d){return -.565 * __r * __get_scale(d);})
			.attr("y", function(d){return -.565 * __r * __get_scale(d);})
			.attr("width", function(d){return 1.13 * __r * __get_scale(d);})
			.attr("height", function(d){return 1.13 * __r * __get_scale(d);})
		svg.selectAll(".node text").data(data.nodes, function(d){return d.id;})
			.attr("y", function(d){return .9 * __r * __get_scale(d);})
			.attr("font-size", .5 * __r);

		force.start();
	};

	if(options.data){
		this.actrel_fr_graph_update(options.data);
	}

	return this;
};
})(jQuery);
