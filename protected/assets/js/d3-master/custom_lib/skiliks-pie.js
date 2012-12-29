skiliksD3Pie = {
    drawPie: function(incomeDataArr, element)
    {
        if(typeof(element) == 'undefined'){
            element = 'body';
        }
        
        $(element).html('');
        
        var width = 400,
        height = 400,
        outerRadius = Math.min(width, height) / 2,
        innerRadius = outerRadius * .6,
        /*innerRadius = 0,*/
        
        
        /*data = d3.range(10).map(Math.random),*/
        data = incomeDataArr,
        
        
        color = d3.scale.category20(),
        donut = d3.layout.pie()
        .sort(null)
        .value(function(d) { return d.value; }),
        arc = d3.svg.arc().innerRadius(innerRadius).outerRadius(outerRadius);

        var vis = d3.select(element)
          .append("svg")
            .data([data])
            .attr("width", width)
            .attr("height", height);

        var arcs = vis.selectAll("g.arc")
            .data(donut)
          .enter().append("g")
            .attr("class", "arc")
            .attr("transform", "translate(" + outerRadius + "," + outerRadius + ")");

          arcs.append("path")
              .attr("d", arc)
              .style("fill", function(d) { 
                  //return color(d.data.name);
                  return d.data.color; 
              });

            arcs.append("text")
              .attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
              .attr("dy", ".35em")
              .style("text-anchor", "middle")
              .style("font-size", "18px")
              .text(function(d) { return d.data.name; });
    }
}