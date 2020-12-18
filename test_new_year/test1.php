<!DOCTYPE html>
<html>
<head>
	<title>Test 1</title>
	<style type="text/css">
		body {
  			background-color: white;
		}

		.frame {
			display: inline-block;
			padding: 0;
			border-radius: 3px;
			font-size: 7em;
			text-shadow: 1px 2px  white;
			color: green;
			text-align: center;
		}
		.frame canvas {
		    display: block;
		    padding: 60px 0px 0px 0px;
		    background-color: white;
		}

		#tree{
			display:block;
			position:absolute;
			left:0%;
			bottom:0%;
			z-index: -1000;
			/*transform:translate(-50%,-50%);*/
		}
	</style>
</head>
<body>
	<div class='frame'>
		<canvas id='tree'></canvas>
	</div>

<script type="text/javascript">
	(function() {
	  var SwirlNode, Tree, TreeSwirl, height, swirls, width,
	    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

	  width = 400;
	  height = 450;

	  swirls = [
	    {
	      color: 'gold',
	      nodes: 300,
	      speed: -1,
	      radius: 3
	    }, {
	      color: 'green',
	      nodes: 200,
	      speed: 1,
	      radius: 1.5
	    }, {
	      color: 'blue',
	      nodes: 80,
	      speed: -3,
	      radius: 6
	    }, {
	      color: 'green',
	      nodes: 250,
	      speed: 3,
	      radius: 3
	    }
	  ];

	  Tree = (function() {
	    function Tree(w, h, swirls) {
	      this.run = __bind(this.run, this);
	      var i;
	      this.width = w;
	      this.height = h;
	      this.canvas = document.getElementById('tree');
	      this.context = this.canvas.getContext('2d');
	      this.canvas.width = w;
	      this.canvas.height = h;
	      this.swirls = (function() {
	        var _i, _ref, _results;
	        _results = [];
	        for (i = _i = 0, _ref = swirls.length; 0 <= _ref ? _i < _ref : _i > _ref; i = 0 <= _ref ? ++_i : --_i) {
	          _results.push(new TreeSwirl(this, swirls[i], i / swirls.length));
	        }
	        return _results;
	      }).call(this);
	      this.run();
	    }

	    Tree.prototype.run = function(t) {
	      if (t == null) {
	        t = 0;
	      }
	      window.requestAnimationFrame(this.run);
	      return this.draw(t);
	    };

	    Tree.prototype.draw = function(t) {
	      var s, _i, _j, _len, _len1, _ref, _ref1, _results;
	      this.context.clearRect(0, 0, this.width, this.height);
	      _ref = this.swirls;
	      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
	        s = _ref[_i];
	        s.drawBack(t);
	      }
	      _ref1 = this.swirls;
	      _results = [];
	      for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
	        s = _ref1[_j];
	        _results.push(s.drawFront(t));
	      }
	      return _results;
	    };

	    return Tree;

	  })();

	  TreeSwirl = (function() {
	    function TreeSwirl(tree, s, offset) {
	      var i;
	      this.tree = tree;
	      this.offset = offset;
	      this.color = s.color;
	      this.speed = s.speed;
	      this.radius = s.radius;
	      this.nodes = (function() {
	        var _i, _ref, _results;
	        _results = [];
	        for (i = _i = 0, _ref = s.nodes; 0 <= _ref ? _i < _ref : _i > _ref; i = 0 <= _ref ? ++_i : --_i) {
	          _results.push(new SwirlNode(this, i / s.nodes));
	        }
	        return _results;
	      }).call(this);
	    }

	    TreeSwirl.prototype.drawBack = function(t) {
	      var n, _i, _len, _ref, _results;
	      _ref = this.nodes;
	      _results = [];
	      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
	        n = _ref[_i];
	        if (n.inBack(t)) {
	          _results.push(n.draw(t));
	        }
	      }
	      return _results;
	    };

	    TreeSwirl.prototype.drawFront = function(t) {
	      var n, _i, _len, _ref, _results;
	      _ref = this.nodes;
	      _results = [];
	      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
	        n = _ref[_i];
	        if (n.inFront(t)) {
	          _results.push(n.draw(t));
	        }
	      }
	      return _results;
	    };

	    return TreeSwirl;

	  })();

	  SwirlNode = (function() {
	    function SwirlNode(swirl, offset) {
	      this.swirl = swirl;
	      this.offset = offset;
	    }

	    SwirlNode.prototype.yPos = function() {
	      var d, od;
	      d = this.t / 800 * this.swirl.speed;
	      od = d + this.offset * this.swirl.tree.height;
	      return (this.swirl.tree.height - od % this.swirl.tree.height) % this.swirl.tree.height;
	    };

	    SwirlNode.prototype.xDeg = function() {
	      return this.yPos() * 5 + 100 * this.swirl.offset;
	    };

	    SwirlNode.prototype.xRad = function() {
	      return this.xDeg() * Math.PI / 60;
	    };

	    SwirlNode.prototype.xPos = function() {
	      return Math.sin(this.xRad()) * this.swirl.tree.width * this.yPos() / this.swirl.tree.height / 3 + this.swirl.tree.width / 2;
	    };

	    SwirlNode.prototype.shade = function() {
	      return (Math.cos(this.xRad()) + 1) / 2;
	    };

	    SwirlNode.prototype.inBack = function(t) {
	      this.t = t;
	      return Math.cos(this.xRad()) > 0;
	    };

	    SwirlNode.prototype.inFront = function(t) {
	      this.t = t;
	      return !this.inBack(t);
	    };

	    SwirlNode.prototype.draw = function(t) {
	      this.t = t - 600;
	      this.drawNode(this.swirl.radius * 0.6, this.shade() + 0.9);
	      this.t = t - 180;
	      this.drawNode(this.swirl.radius * 0.8, this.shade() + 0.4);
	      this.t = t;
	      return this.drawNode(this.swirl.radius, this.shade());
	    };

	    SwirlNode.prototype.drawNode = function(size, shade) {
	      var c;
	      c = this.swirl.tree.context;
	      c.beginPath();
	      c.arc(this.xPos(), this.yPos(), size, 0, 2 * Math.PI);
	      c.fillStyle = this.swirl.color;
	      c.fill();
	      c.fillStyle = "rgba(0,0,0," + (shade - 0.7) + ")";
	      return c.fill();
	    };

	    return SwirlNode;

	  })();

	  new Tree(width, height, swirls);

	}).call(this);

</script>
</body>
</html>