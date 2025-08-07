<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Examples for bootstrap-slider plugin">
    <meta name="author" content="">

    <title>Slider for Bootstrap Examples Page</title>

    <!-- core CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
    <link href="/Bootstrap/bootstrap-slider.css" rel="stylesheet">
 <link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
   <!-- Hightlight.js Theme Styles -->
    <link href="/awe/highlightjs-github-theme.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <style type='text/css'>

    	/* Space out content a bit */
			body {
			  padding-top: 20px;
			  padding-bottom: 20px;
			}

			h1 small {
				font-size: 51%;
			}

			table {
				border-collapse: collapse;
				width: 100%;
			}

			th, td {
				text-align: left;
				padding: 5px;
			}

			tr:nth-child(even){background-color: #e5e5e5}

			th {
				background-color: #00008B;
				color: white;
			}

			/* Everything but the jumbotron gets side spacing for mobile first views */
			.header,
			.marketing,
			.footer {
			  padding-left: 15px;
			  padding-right: 15px;
			}

			/* Custom page header */
			.header {
			  border-bottom: 1px solid #e5e5e5;
			}
			/* Make the masthead heading the same height as the navigation */
			.header h3 {
			  margin-top: 0;
			  margin-bottom: 0;
			  line-height: 40px;
			  padding-bottom: 19px;
			}

			/* Custom page footer */
			.footer {
			  padding-top: 19px;
			  color: #777;
			  border-top: 1px solid #e5e5e5;
			}

			/* Customize container */
			.container {
				min-width: 640px;
			}
			@media (min-width: 768px) {
			  .container {
			    max-width: 1000px;
			  }
			}
			.container-narrow > hr {
			  margin: 30px 0;
			}

			/* Main marketing message and sign up button */
			.title {
			  text-align: center;
			  border-bottom: 1px solid #e5e5e5;
			}

			/* Responsive: Portrait tablets and up */
			@media screen and (min-width: 768px) {
			  /* Remove the padding we set earlier */
			  .header,
			  .footer {
			    padding-left: 0;
			    padding-right: 0;
			  }
			  /* Space out the masthead */
			  .header {
			    margin-bottom: 30px;
			  }
			  /* Remove the bottom border on the jumbotron for visual effect */
			  .title {
			    border-bottom: 0;
			  }
			}

			.card {
				background-color: #e0e0e0;
			}

			.slider-example {
				padding-top: 10px;
				padding-bottom: 55px;
				margin: 35px 0;
			}

			#destroyEx5Slider, #ex6CurrentSliderValLabel, #ex7-enabled {
				margin-left: 45px;
			}

			#ex6SliderVal {
				color: green;
			}

			#slider12a .slider-track-high, #slider12c .slider-track-high {
				background: green;
			}

			#slider12b .slider-track-low, #slider12c .slider-track-low {
				background: red;
			}

			#slider12c .slider-selection {
				background: yellow;
			}

			#slider22 .slider-selection {
				background: #2196f3;
			}

			#slider22 .slider-rangeHighlight {
				background: #f70616;
			}

			#slider22 .slider-rangeHighlight.category1 {
				background: #FF9900;
			}

			#slider22 .slider-rangeHighlight.category2 {
				background: #99CC00;
			}

    </style>

    <style type='text/css'>
			/* Example 1 custom styles */
			#ex1Slider .slider-selection {
   			background: #BABABA;
  		}

    	/* Example 3 custom styles */
			#RGB {
    		height: 20px;
    		background: rgb(128, 128, 128);
  		}
			#RC .slider-selection {
			    background: #FF8282;
			}
			#RC .slider-handle {
				background: red;
			}
			#GC .slider-selection {
				background: #428041;
			}
			#GC .slider-handle {
				background: green;
			}
			#BC .slider-selection {
				background: #8283FF;
			}
			#BC .slider-handle {
				border-bottom-color: blue;
			}
			#R, #G, #B {
				width: 300px;
			}
    </style>

	<script type='text/javascript' src="/js/modernizr.js"></script>
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
    <!-- Highlight.js Styles -->
  </head>

  <body>

    <div class="container">

      <div id="top" class="jumbotron">
        <h1>Slider for Bootstrap <small>bootstrap-slider.js</small></h1>
        <p class="lead">
			Examples for the <a target="_blank" href="https://github.com/seiyria/bootstrap-slider">bootstrap-slider</a> component.
			</br>
			</br>
			Now compatible with <a target="_blank" href="https://getbootstrap.com/docs/4.0/getting-started/introduction/">Bootstrap 4</a>
		<p>
      </div>

	  <table>
		<tr>
		  <th>Example Link</th>
		  <th>Example Description</th>
		</tr>
		<tr>
		  <td><a href="#example-1">Example 1</a></td>
		  <td>Basic example with custom formatter and colored selected region via CSS</td>
		</tr>
      </table>

      <div class="examples">
      	<div id="example-1" class='slider-example'>
      		<h3>Example 1: <a href="#top"><small>Back to Top</small></a></h3>
      		<p>Basic example with custom formatter and colored selected region via CSS.</p>
      		<div class="card card-body mb-3">
				<input id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="14"/>
			</div>
<h5>HTML</h5>
<pre><code class="html">
&ltinput id="ex1" data-slider-id='ex1Slider' type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="14"/&gt
</code></pre>

<h5>JavaScript</h5>
<pre><code class="js">
// With JQuery
$('#ex1').slider({
	formatter: function(value) {
		return 'Current value: ' + value;
	}
});

// Without JQuery
var slider = new Slider('#ex1', {
	formatter: function(value) {
		return 'Current value: ' + value;
	}
});
</code></pre>

<h5>CSS</h5>
<pre><code class="css">
#ex1Slider .slider-selection {
	background: #BABABA;
}
</code></pre>
      	</div>

	  </div> <!-- /examples -->
    </div> <!-- /container -->


    <!-- core JavaScript
    ================================================== -->
	<script defer src="./awe/js/all.js" ></script>
 <script src="./Bootstrap/popper.min.js"</script>
<script src="./Bootstrap/bootstrap.min.js"></script>
   <script src="/Bootstrap/jquery.min.js"></script>
    <script src="/js/bootstrap-slider.min.js"></script>
    <script src="/js/highlight.min.js"></script>
    <script>hljs.initHighlightingOnLoad();</script>
    <script>
    	$(document).ready(function() {

    		/* Example 1 */
	    	$('#ex1').slider({
	          	formatter: function(value) {
	            	return 'Current value: ' + value;
	          	}
	        });

		});
    </script>
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>

