	<style>
		#main_nav{
			display:block;
		}
			h1 {
				color: red;
				font-size: 200%;
				font-weight: medium;
			}
			h2 {
				color: black;
				font-size: 160%;
				vertical-align: text-bottom;
			}
			h2c {
				color: black;
				font-size: 160%;
				vertical-align: text-bottom;
				display:block;
				align-items: center;
			    justify-content: space-around;
			    display: flex;
			    float: none;
			}
			h3c {
				color: black;
				font-size: 110%;
				vertical-align: text-bottom;
				display:block;
				align-items: center;
			    justify-content: space-around;
			    display: flex;
			    float: none;
			}
			h3 {
				color: white;
				font-size: 120%;
				vertical-align: text-bottom;
			}
			h4{
				color: black;
				font-size: 200%;
				background: #aaa;
			}
			h5{
				color: gray;
				font-size: 70%;
			}
			span { 
			    display: inline-block; 
			    vertical-align: middle;
			    color:#000; 
			    background: #fff; 
			    font-size: 20px;
				padding: 1px 1px 1px 1px;
			    border-radius: 2px;
			}
			freq { 
			    display: inline-block; 
			    vertical-align: middle;
			    color:#000; 
			    background: orange; 
			    font-size: 400%;
				padding: 1px 1px 1px 1px;
			    border-radius: 2px;
				font-family: 'Arial';
			}
			spans { 
			    display: inline-block; 
			    vertical-align: bottom;
			    color:#000; 
			    background: orange; 
			    font-size: 300%;
				padding: 20px 1px 1px 1px;
			    border-radius: 2px;
				font-family: 'Arial';
			}
			.space{
				background-color:red;
				}
			.top-buffer {
				margin-top: 10px;
			}
			.btn-padding {
				padding: 2px !important;
				margin: 0x !important;
				text-align: center;
			}
			.btn-padding-macro {
				padding: 2px !important;
				text-align: center;
				font-size: 8px;
				width: 100%;
				margin: 10x !important;
			}
			.btn-stacked {
				padding: 2px !important;
				margin: 0px !important;
				text-align: center;
			}
			.selector-padding {
				display: block;
				padding: 5px;
				padding-left: 0px;
				float: none;
				margin: 0 auto;
			}
			.btn-color {
				background: black;
				border: none;
				color: white;
				height: 38px;
				outline: none;
				}
			.btn-sm {
				border: light;
				margin-bottom: -2px !important;
				}
			.btn-small-color {
				background: transparent !important;
				padding: .25ren .4rem;
				border: light;
				color: white;
				line-height: .4;
				outline: light;
				font-size: .5rem;
				border-radius: .2rem;
				}
         
			.btn-small-lock-on{
				background: red !important;
				color: white;
				}
			.btn-small-lock-off{
				background: transparent !important;
				color: white;
				}

			.clickme{
				cursor:default;
			}
			.btn-color:hover {
				cursor: default;
				}
			.btn-info {
				border: none;
				color: white;
				height: 38px;
				outline: none;
				}
		.btn-success {
			border: none;
			color: white;
			height: 38px;
			outline: none;
			}
		.btn-secondary {
			border: none;
			color: white;
			height: 38px;
			outline: none;
			}
			.btn-danger {
				border: none;
				color: white;
				height: 38px;
				outline: none;
				}
			.form-control {
				border: none;
				}
			.form-control-plaintext {
				border: none;
				}
			.btn-primary {
				background: black;
				border: black;
				color: white;
				height: 38px;
				}
			.text-white{
				background:#444;
				color: white;
				font-size: 20px;
			}
			.text-white-small{
				background:#444;
				color: white;
				font-size: 15px;
			}
			.text-white-small-right{
				background:#444;
				color: white;
				font-size: 15px;
				text-align:right;
			}
			.text-white-big{
				background:#444;
				color: white;
				font-size: 40px;
				text-align:center; 
			}
			.dMeter{
				text-align:center; 
			}
			.text-white-medium{
				background:#444;
				color: white;
				font-size: 30px;
				text-align:center; 
			}
			.body-black {
				background: #444;
				cursor: pointer !important!;
			}
			.body-white {
				background: #FFF;
				cursor: pointer !important!;
			}
			.dPanel{
				background-color:red;
				float: none;
			}
			.radio-panel{
				background:orange;
				font-size: 200%;
				font-weight:600 ;
			}
			.radio-top-panel{
				background:orange;
				height: 50px;
				text-align: center;
			}
			.XXXknob-panel{
				background:orange;
				height: 100 px;
				float: none;
				margin: 0 auto;
			}
			.wPix{
				width: 50%; 
				height: 50%;
			}
			.dKnob{
			    display: flex;
			    float: none;
			    margin-top: 40;
			}
			.disable-text{
				background-color:#bbb !important;
				border:1px solit white;
			}
			.split-text{
				background:orange;
				text-align: center;
				font-size: 100%;
			}
			.wlbl{
				color: white;
				font-size: 110%
			}

			table{ 
				border-right:1px solid #ccc; 
				border-bottom:1px solid #ccc;
			}
			table th{
				background:#aaa; 
				padding:5px; 
				border-left:1px solid #ccc; 
				border-top:1px solid #ccc;
				font-size: 100%;
			}
			table .tde{
				background:#fff; 
				padding:5px; 
				border-left:1px solid #ccc; 
				border-top:1px solid #ccc;
				font-size: 100%;
			}
			table .tdo{
				background:#bae8f1; 
				padding:5px; 
				border-left:1px solid #ccc; 
				border-top:1px solid #ccc;
				font-size: 100%;
			}
			table td{ 
				padding:5px; 
				border-left:1px solid #ccc; 
				border-top:1px solid #ccc;
   			    text-align: center;
				background:white;				
			}
			big_bg {
				display: block;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				width: 100%;
				height: 100%;
			}
			.sortable th {
			    cursor: pointer;
			}
			}
			table.bigdemo {
				border-collapse: collapse;
			}
			table.bigdemo td > label {
				display: block;
			}
			.label {
				cursor: auto;
				user-select: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none
			}
			.table {
				cursor: default;
				user-select: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none
			}
			.ibox {
				margin: 0 auto;
				margin-left: auto;
				margin-right: auto;}
			.input-group-text {
				cursor: default;
				user-select: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none
			}
			table.bigdemo th {
				text-align: right;
				font: inherit;
				padding-left: 0.5em;
				padding-right: 0.5em;
			}
			.big_container {
				position: relative;
				padding: 0;
			}
			.big_bg {
				display: block;
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				width: 100%;
				height: 100%;
			}
			.big {
				display: block;
				position: relative;
				width: 13em;
				height: 13em;
				background-repeat: no-repeat;
				background-size: contain;
				background-position: center center;
			}
			div.padding2{
				padding-right: 20px;
			}
			
			.sizes x-knob {
				outline: 1px blue dotted;
			}
			.sizes x-knob:nth-child(1) { width: 1em; height: 1em; }
			.sizes x-knob:nth-child(2) { width: 2em; height: 2em; }
			.sizes x-knob:nth-child(3) { width: 4em; height: 2em; }
			.sizes x-knob:nth-child(4) { width: 2em; height: 4em; }
			.sizes x-knob:nth-child(5) { width: 4em; height: 4em; }
			
			.center {
				margin: auto;
			}
			.content {
			  position: absolute;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  overflow: auto;
			  text-align: center;
			  vertical-align: text-top;
			}			
			.col-centered{
			  float: none;
			  margin: 0 auto;
			}
			
			.colx-centered{
			  position: absolute;
			  overflow: auto;
			  text-align: center;
			}
			
			.checkbox input[type=checkbox],
			.checkbox-inline input[type=checkbox],
			.radio input[type=radio],
			.radio-inline input[type=radio] {
			  margin: 0;
			}
			
			.checkbox,
			.radio {
			  position: relative;
			  display: block;
			  margin-top: 10px;
			  margin-bottom: 10px;
			  display: table;
			  width: 100%;
			}
			
			.input-group-text {
				color: white;
				min-width: 100px;
				background: #888;
			}

			.input-group-rotor {
				color: white;
				min-width: 80px;
				background: #888;
			}

			.system-group-addon {
				color: white;
				min-width: 100px;
				background: #888;
			}

			.macro-group-addon {
				color: white;
				min-width: 100px;
				background: #0A0;
			}

			.default-added{
				color: white;
				min-width: 100px;
				background: #0A0;
			}

			.noedit{
				color: white;
				min-width: 100px;
				background: #A00;
			}


			.keyer-group-addon {
				color: white;
				min-width: 100px;
				background: #888;
			}

			.radio-group-addon {
				color: white;
				min-width: 100px;
				background: #888;
			}

			.log-group-addon {
				color: white;
				min-width: 90px;
				background: #888;
			}

			.checkbox input,
			.radio input {
			  vertical-align: middle;
			  height: 100%;
			}
			
			.menu-scroll {
			    overflow-y: scroll;
			    max-height: 200px;
			}
			.custom-file-control:after {
			  content: "Select ADI file to Import..." !important;
			}
			
			.custom-file-control:before {
			  content: "Browse";
			}
			.custom-file{
			  overflow: hidden;
			}
			.custom-file-control {
			  white-space: nowrap;
			}
			.hButton{
			    position:relative;
			    float:right;
				margin-right:40px;
			    cursor: pointer;
			}

			.dropdown-ham::after {
			    display:none;
			}
			.page-item {
			    margin:0;
			}
			.logButton {
			    width: 78px !important;
			}
			
			.text-spacer{
				 margin-top:15px;
			}
			
			.fixed{
				width:320px;
			}
			
		  #sortable1{
			max-height: 400px;
			height: auto;
			overflow-x: hidden;
		    border: 1px solid #eee;
		    width: 250px;
		    min-height: 20px;
		    list-style-type: none;
		    margin: 0;
		    padding: 5px 0 0 0;
		    float: left;
		    margin-right: 10px;
		  }

		  #sortable2 {
			max-height: 400px;
			height: auto;
			overflow-x: hidden;
		    border: 2px solid #f00;
		    width: 250px;
		    min-height: 20px;
		    list-style-type: none;
		    margin: 0;
		    padding: 5px 0 0 0;
		    float: left;
		    margin-right: 10px;
		  }

		  #sortable3 {
			max-height: 400px;
			height: auto;
			overflow-x: hidden;
		    border: 2px solid #0f0;
		    width: 250px;
		    min-height: 20px;
		    list-style-type: none;
		    margin: 0;
		    padding: 5px 0 0 0;
		    float: left;
		    margin-right: 10px;
		  }

		  #sortable1 li {
		    margin: 0 5px 5px 5px;
		    padding: 5px;
		    font-size: 1.2em;
		    width: 238px;
		  }			
			
		  #sortable2 li, #sortable3 li {
		    margin: 0 5px 5px 5px;
		    padding: 5px;
		    font-size: 1.2em;
		    width: 238px;
		  }			
			
			body.dragging, body.dragging * {
			  cursor: move !important;
			}
			
			.dragged {
			  position: absolute;
			  opacity: 0.5;
			  z-index: 2000;
			}
			
			ol.connectedSortable li.placeholder:before {
			  position: static;
			  /** Define arrowhead **/
			}
			
			.b-red{
				color: red;
			}
			
			.tr-alt{
				float: right;
				margin-right: 10px;
			}
			
			.spacer{
				margin-left:10px;			
			}
			
			.timeUTC{
				color: gray;
				font-size: 150%;
				margin-left:0; 
			}
			
			.noselect {
			  -webkit-touch-callout: none; /* iOS Safari */
			    -webkit-user-select: none; /* Safari */
			     -khtml-user-select: none; /* Konqueror HTML */
			       -moz-user-select: none; /* Firefox */
			        -ms-user-select: none; /* Internet Explorer/Edge */
			            user-select: none; /* Non-prefixed version, currently
			                                  supported by Chrome and Opera */
			}
			
			.modal-header{
			    background-color: #e3f2fd;	
			}
			
			.web-header{
			    height: 200px;; 
			    background-color: #e3f2fd;	
			}
			
			.modal-title{
			    margin-top: 0px; 
			    background-color: #e3f2fd;	
			}

			#modalA-header{
			    background-color: #e3f2fd;	
			    background-color: #e3f2fd;	
			}
			
			#modalA-title{
			    margin-top: 0px; 
			    background-color: #e3f2fd;	
			}
			#modalI-body{
				background-color: #666666;
			}
			.web-title{
			    margin-top: 0px; 
			    background-color: #f3ffff;	
			}

			.web-filler{
			    height: 20px; 
			    background-color: #f3ffff;	
			}
			
			.web-header{
			    background-color: #f3ffff;	
				display:block;
			    margin: 0 auto;
			}
			
			.web-pix{
			    background-color: #f3ffff;
			}
			
			.web-body{
			    background-color: #f3ffff;
			    height: 600;	
			}
			
			.flag{
			    background-color: #f3ffff;	
				margin: 0 auto;
				display:block;
			}

			.modal-pix{
			    background-color: #f3ffff;	
				margin: 0 auto;
				display:block;
			}

			.map{
				height: 400;
			    background-color: #f3ffff;	
				margin: 0 auto;
				display:block;
			}
			
			.thin {
				margin-top: 5px;
				margin-bottom: -2px;
			}
			
			.bs {
			  margin: 0;
			  overflow: hidden;
			  height:100%;
			}
			
			.sp {
			  margin: 0;
			  overflow: hidden;
			  height:100%;
			}
			
			#bandCanvas {
			    position:absolute;
			    display:inline-block;
			}
			
			.BSbutton {
			    position:absolute;
			    left: 180px;
			    outline: 2px solid black;
			    right:10px;
			    width:140px;
			    height:30px;
				border-radius: 0 !important;
			}
			
			.BSFrequency{
				position:absolute;
 			    left:30px;
 			    font-size: 20px;
 			    color:white;
 			    zindex:-1000;
			}
			
			.box{
			    position:absolute;
			    top: 100px;
			    left:15%;
			    width:40%;
			    height:100px;
			}

			.active a {
				color:#fff !important;
			    background-color:#3d98f2;
			    background-image: none;
			    background-repeat: no-repeat;
			}
			 
			.table-striped tbody tr:nth-child(odd) td {
				cursor: pointer;
			 	background-color: #bae8f1;
			}

			.table-striped tbody tr:nth-child(even) td {
				cursor: pointer;
			}

			.table-striped tbody tr.highlight td {
				cursor: pointer;
				background-color: red;
				font-weight: bolder;
				font-size: larger;
			}

			.table-striped tbody td.highlight td {
				cursor: pointer;
				background-color: red;
				font-weight: bolder;
				font-size: larger;
			}

			/*Bootstrap button outline override*/
			.btn-outline {
			    color: white;
			    transition: all .5s;
			}
			
			.btn-primary.btn-outline {
			    color: white;
			}
			
			.btn-success.btn-outline {
			    color: #5cb85c;
			}
			
			.btn-info.btn-outline {
			    color: #5bc0de;
			}
			
			.btn-warning.btn-outline {
			    color: #f0ad4e;
			}
			
			.btn-danger.btn-outline {
			    color: white;
			}
			
			.btn-primary.btn-outline:hover,
			.btn-success.btn-outline:hover,
			.btn-info.btn-outline:hover,
			.btn-warning.btn-outline:hover,
			.btn-danger.btn-outline:hover {
			    color: white;
			}
		.btn-pointer:hover{
            cursor: pointer;
        }
			
        .nav-item a:hover {
            background-color: #5bc0de !important;
            color: white !important;
            cursor: pointer;
        }
        
        .footer {
	        position: fixed;
	        left: 0;
	        bottom: 0;
	        width: 100%;
	        background-color: black;
	        color: white;
	        z-index:1000;
	        }
	    #fPanel1 {
	    }
	    #fPanel2 {
		    text-align: left;
	    }
	    #fPanel3 {
		    text-align: left;
	    }
	    #fPanel4 {
	    	text-align: left;
	    }
		body {
		/* Margin bottom by footer height */
			margin-bottom: 60px;
		}
		body.noScroll { 
			overflow-y:hidden;
  			position:fixed;
  			width: 100%;
		}
		    
		.custom-file-control:before {
			content: "Search";
		}
		
		div.inline {
			float: right;
			margin-left: 10px;
		}
		
		li.dropdown-header {
	    	text-align: center;
			font-size: 16px;
			background-color: gray;
			color: #ffffff;
		}
		
		.dropdown-submenu {
			position: relative;
		}

		.dropdown-submenu a::after {
		  transform: rotate(-90deg);
		  position: absolute;
		  right: 6px;
		  top: .8em;
		}

		.dropdown-submenu .dropdown-menu {
		  top: 0;
		  left: 100%;
		  margin-left: .1rem;
		  margin-right: .1rem;
		}
		
	hr {
       display: block;
       position: relative;
       padding: 0;
       margin: 8px auto;
       height: 0;
       width: 100%;
       max-height: 0;
       font-size: 1px;
       line-height: 0;
       clear: both;
       border: none;
       border-top: 1px solid #aaaaaa;
       border-bottom: 1px solid #ffffff;
    }
    
    #dt {
	  background: transparent;
	  color: white;
	  resize: none;
	  border: 0 none;
	  width: 1px;
	  font-size: 1px;
	  outline: none;
	  height: 1px;
	  position: absolute;
	}
	
	#overlay {
  position: fixed;
  height: 100%;
  width: 100%;
  z-index: 1000000;

}

	#modalAlertOK, #logoutButton, #modalAlertClose, #closeUpdate{
		z-index: 999;
	}
	
	#modalAlertClose{
		z-index: 999;
	}
	
	.rigvid {
		border: 1px solid white;
	}
	
.table-smxx td{
	padding: 1px; 
	text-align: center;
	background: #444;				
}

.table-smxy td{
	padding: 3px; 
	text-align: center;
	background: #444;
	border: none;				
}

.table-smxx{
	border: none;
	width: 100%;
	min-width: 800px;
	max-width: 3800px;
		
}

.table-smxy{
	border: none;
	width: 100%;
background: #444;		
}

	.table-responsive{
		overflow-y: hidden;
		overflow-x: auto;
		}
	.btn-color:hover {
		background-color: blue !important;
		color: white;
	}
	
	.slidecontainer {
		width: 60%;
		float: right;
		margin-top: 12px;
		height: 25px;
	}

	.slidecontainerSpeed {
		width: 70%;
		float: right;
		margin-top: 10px;
		height: 35px;
		margin-right: 10px;
	}
#sliderSpeed {
    height: 15px;
	margin-left: 0px;
    margin-right: 10px;
    border:none;
}
 #sliderSpeed .ui-slider-handle {margin-left: -5px;background: green;border:none;}
#sliderAF {
    height: 15px;
    margin-left: 0px;
    margin-right: 10px;
    border:none;
}
 #sliderAF .ui-slider-handle {background: green;border:none;}
#sliderRF {
    height: 15px;
    margin-left: 0px;
    margin-right: 10px;
    border:none;
color:#000;
}
 #sliderRF .ui-slider-handle {background: green;border:none;}
#sliderPwrOut {
    height: 15px;
    margin-left: 0px;
    margin-right: 10px;
    border:none;
}
 #sliderPwrOut .ui-slider-handle {background: green;border:none;}
#sliderMic {
    height: 15px;
    margin-left: 0px;
    margin-right: 15px;
    border:none;
}
 #sliderMic .ui-slider-handle {background: green;border:none;}
 
 
</style>
