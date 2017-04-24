/*!
	* Chart24.js
	* Version 1.0.0 
	* Last Update 2015 November 6
	* CopyRight 2015 Kim Min Seok
*/

var chart24 = {
	loadDefault: function(){
		this.jsel = null;
		this.sel = null;
		this.csel = null;
		this.c = null;
		this.data = null;
		this.labelData = null,
		this.dots = [];
		this.options = {
			width: null,
			height: null,

			// Tooltip
			tooltip: true,
			tooltipBackground: "rgba(0,0,0,.7)",
			tooltipBorderRadius: "5px",
			tooltipColor: "#fff",
			tooltipAnimate: true,

			// Color
			borderColor: "#"+"e6e6e6",
			valueLineColor: "#"+"e02d23",
			backgroundColor: "#"+"ffffff",
			
			// Width
			borderLineWidth: 1,
			valueLineWidth: 1,

			// xAxis
			xAxis: true,
			xPadding: 70,
			xAxisNumMargin: 10,
			xAxisLineOverflow: 5,

			// yAxis
			yAxis: true,
			yPadding: 50,
			yAxisNum: 10,
			yAxisNumMargin: 10,
			yAxisLineOverflow: 5,

			// Labels
			showLabel: true,

			// Data Arc
			dataArc: true,
			dataArcRadius: 5,
			dataArcColor: "#"+"e02d23",
			dataArcBorderColor: "#"+"ffffff",
			dataArcBorderWidth: 3,

			max: null,
			min: null
		};
	},
	init: function(sel, data, options){
		this.loadDefault();
		this.setSelector(sel);
		this.setOptions(options);
		this.setData(data);
		this.start();
	},
	existVal: function(val){
		if(typeof val === "undefined")
			return false;
		else
			return true;
	},

	// Start
	start: function(){
		this.eleClear();
		this.eleCreate();
		this.setHandler();
		this.canvasDefaultSet();
		this.setCVSFunctions();

		this.getMaxValue();
		this.getMinValue();

		if(this.options.yAxis)	this.drawYAxis();
		if(this.options.xAxis)	this.drawXAxis();

		this.drawData();		
		if(this.options.dataArc)	this.drawDataArc();		
	},


	eleClear: function(){
		this.jsel.html("");
	},
	eleCreate: function(){
		$("<canvas>").attr({
			id: "chart24_"+this.sel,
			width: this.options.width,
			height: this.options.height,
		}).appendTo("#"+this.sel);

		$("<div>").attr({
			id: "chart24_"+this.sel+"_tooltip"
		}).css({
			padding: "7px 10px", 
			position: "absolute",
			left: 0,
			top: 0,
			background: this.options.tooltipBackground,
			color: this.options.tooltipColor,
			"z-index": "100	",
			"border-radius": this.options.tooltipBorderRadius,
			transform: "translateX(-50%)",
			// transition: this.options.tooltipAnimate ? "left .1s, top .1s" : "",
		}).hide().html("<span></span>").append(
			$("<div>").attr({
				id: "chart24_"+this.sel+"_tooltip_arr"
			}).css({
				width: 0,
				height: 0,
				position: "absolute",
				left: "50%",
				top: "100%",
				transform: "translateX(-50%)",
				borderLeft : ' 7px solid transparent',
				borderRight : ' 7px solid transparent',
				borderTop : '10px solid '+this.options.tooltipBackground,
			})
		).appendTo("#"+this.sel);
	},

	// Canvas Set
	canvasDefaultSet: function(){
		this.c = $("#"+this.csel)[0].getContext("2d");
	},
	
	// Canvas Draw
	drawXAxis: function(){
		var c = this.c;


		c.beginPath();
		c.stp(); // 0,0 으로 이동
		c.lineTo(this.getX(this.options.width), this.getY(0)+0.5);
		c.strokeStyle = this.options.borderColor;
		c.lineWidth = this.options.borderLineWidth;
		c.stroke();

		this.drawXAxisNum();
	},
	drawXAxisNum: function(){
		var c = this.c;
		c.beginPath();
		c.setFont();

		c.strokeStyle = this.options.borderColor;
		c.lineWidth = this.options.borderLineWidth;

		c.textAlign = "center";
		c.textBaseline = "hanging";

		var xPos;
		var maxXPos = this.options.width - this.options.xPadding;

		for(var i = 0; i < this.data.length ; i++){

			xPos = maxXPos/(this.data.length-1)*i;
			c.fillText(i+1/*this.data[i]*/, this.getX(xPos), this.getY(0 - this.options.xAxisNumMargin));
			this.drawXAxisLine(this.getX(xPos));

		}
		c.stroke();
	},
	drawXAxisLine: function(x){
		this.c.moveTo(x, this.getY(0 - this.options.xAxisLineOverflow));
		this.c.lineTo(x, this.getY(this.options.height));
	},

	drawYAxis: function(){
		var c = this.c;

		c.beginPath();
		c.setFont();

		c.stp();
		c.lineTo(this.getX(0)+0.5, this.getY(this.options.height));
		c.strokeStyle = this.options.borderColor;
		c.lineWidth = this.options.borderLineWidth;
		c.stroke();

		this.drawYAxisNum();
	},
	drawYAxisNum: function(){
		var c = this.c;
		c.beginPath();
		c.setFont();

		c.strokeStyle = this.options.borderColor;
		c.lineWidth = this.options.borderLineWidth;

		c.textAlign = "right";
		c.textBaseline = "middle";

		var yPos = 0;
		var maxYPos = this.options.height - this.options.yPadding;

		var val = this.options.min;
		var interval = (this.options.max-this.options.min) / this.options.yAxisNum;

		for(var i = 0; i <= this.options.yAxisNum ; i++){
			c.fillText(this.comma(val.toFixed(1)), this.getX(0-this.options.yAxisNumMargin), this.getY(yPos));
			this.drawYAxisLine(this.getY(yPos));
			val += interval;
			yPos += maxYPos / this.options.yAxisNum;
		}
		c.stroke();
	},
	drawYAxisLine: function(y){
		this.c.moveTo(this.getX(0 - this.options.yAxisLineOverflow), y +0.5);
		this.c.lineTo(this.getX(this.options.width), y + 0.5);
	},



	drawData: function(){
		var c = this.c;
		c.beginPath();

		c.strokeStyle = this.options.valueLineColor;
		c.lineWidth = this.options.valueLineWidth;

		var xPos;
		var maxXPos = this.options.width - this.options.xPadding;

		c.moveTo(this.getX(0), this.getY( (this.options.height-this.options.yPadding) * (this.data[0]-this.options.min) / (this.options.max - this.options.min)));
		for(var i = 0; i < this.data.length ; i++){
			xPos = maxXPos/(this.data.length-1)*(i+1);
			c.lineTo(this.getX(xPos), this.getY( (this.options.height-this.options.yPadding) * (this.data[i+1]-this.options.min) / (this.options.max - this.options.min)));	
		}
		c.stroke();
	},
	drawDataArc: function(){
		var c = this.c;
		c.fillStyle = this.options.dataArcColor;
		c.lineWidth = this.options.dataArcBorderWidth;
		c.strokeStyle = this.options.dataArcBorderColor;

		var xPos;
		var maxXPos = this.options.width - this.options.xPadding;

		for(var i = 0; i < this.data.length ; i++){		
			c.beginPath();
			xPos = maxXPos/(this.data.length-1)*(i);
			c.arc(this.getX(xPos), this.getY((this.options.height-this.options.yPadding) * (this.data[i]-this.options.min) / (this.options.max - this.options.min)), this.options.dataArcRadius, 0, 2 * Math.PI, false);	
			c.fill();
			c.stroke();

			this.dots.push({x: this.getX(xPos), y: this.getY((this.options.height-this.options.yPadding) * (this.data[i]-this.options.min) / (this.options.max - this.options.min))});
			$(".log").append(this.dots[i].x + " / " + this.dots[i].y + "<br />");
		}
		this.dots[this.data.length-1].x -= 3;
	},
	
	// Getter 
	getX: function(x){
		return x + this.options.xPadding;
	},
	getY: function(y){
		return this.options.height - y - this.options.yPadding;
	},
	getMaxValue: function(){
		if(this.options.max != null)
			return false;

		var max = 0;
		for(i in this.data){
			i = this.data[i];
			if(i > max){
				max = i;
			}
		}
		this.options.max = max*1.05;		
		return max;
	},
	getMinValue: function(){
		if(this.options.min != null)
			return false;

		var min = this.data[0];
		for(i in this.data){
			i = this.data[i];
			if(i < min){
				min= i;
			}
		}
		this.options.min = min*0.95;
		return min;
	},
	comma: function(str) {
		str = String(str);
		return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
	},
	// Setter
	setHandler: function(){
		if(this.options.tooltip){
			$("#chart24_"+this.sel).mousemove(function(e){
	//			chart24.mouse.y = e.offsetY;
				if($("#chart24_"+this.sel+""))
				chart24.tooltipMoveEvent(e.offsetX);
			});
		}

	},
	setCVSFunctions: function(){
		this.c.setFont = function(){
			chart24.c.font = "12px Malgun Gothic";
		};
		this.c.stp = function(){
			chart24.c.moveTo(chart24.getX(0)+0.5, chart24.getY(0)+0.5);
		};
	},
	setSelector: function(sel){
		this.jsel = $("#"+sel);
		this.csel = "chart24_"+sel;
		this.sel = sel;
	},
	setOptions: function(options){
		this.options.width = this.jsel.width();
		this.options.height = this.jsel.height();

		for(var i in options) {
			this.options[i] = options[i];
		}
	},
	setData: function(data){
		this.data = data;
	},

	// Events
	tooltipMoveEvent: function(x){
		for(var i in this.dots){
			if(x == parseInt(this.dots[i].x)){
				$("#chart24_"+this.sel+"_tooltip").css({
					left: this.dots[i].x,
					top: this.dots[i].y - 50,
				}).show().find("span").html(this.comma(this.data[i]));
			}
		}
	}
}; 