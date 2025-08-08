<style>
		#main_nav{
			display:block;
		}

			h1 {
				color: red;
				font-size: 200%;
				font-weight: medium;
				text-align: center;
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
			h3b {
				color: white;
				font-size: 16px;
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
			calendar-scheduler {
				padding: 300px;
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
			outputSpeed {
			font-size: 5px;
	
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
				text-align: center;
				font-size: 8px;
				width: 100%;
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
			.btn-secondary {
			border: none;
			color: white;
			height: 38px;
			outline: none;
			background-color: gray;
			}
			.btn-secondary:hover {
			color: black;
			height: 38px;
			outline: none;
			background-color: rgba(0, 200, 200, 1.0);
			}
			
			.btn-color {
				background: black;
				border: none;
				color: white;
				height: 38px;
				outline: none;
				
				}
			.btn-mode-sel {
				background: #b9cfd2 !important;
				border: none;
				color: black !important;
				height: 38px;
				outline: none;
				}

			.btn-sm {
				border: light;
				margin-bottom: -2px !important;
				}
			.btn-sm-mac {
				border: light;
				margin-bottom: -2px !important;
				max-width:100%;
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
			.btn-small-success-on{
				background: green !important;
				padding: 2px !important;
				margin: 0px !important;
				text-align: center;
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
			.btn-info {  //mode buttons
				border: none;
				color: white;
				height: 38px;
				outline: none;
			}
		.btn-success { //band buttons
				border: none;
				color: white;
				height: 38px;
				outline: none;
				}
		.btn-success:hover {
				border: none;
				color: black;
				height: 38px;
				outline: none;
				background-color: darkgray;
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
				overflow: hidden;
			}
			.body-black-scroll {
				background: #444;
				cursor: pointer !important!;
				overflow-y: auto;
				overflow-x: hidden;
				z-index: 10;
			}
			.body-white-scroll {
				background: #fff;
				cursor: pointer !important!;
				overflow-y: auto;
				overflow-x: hidden;
				z-index: 10;
			}
			.body-black-log {
				background: #444;
				cursor: pointer !important!;
				overflow: hidden;
			}
			body-spots {
				overflow-x: hidden;

//			  overflow-y: scroll;        /* Force vertical scroll */
				background: #444 !important;
			  cursor: pointer !important!;				
			}

			body-black.noscroll {
				background: #444 !important;
				cursor: pointer !important!;

			  -ms-overflow-style: none;  /* IE 10+ */
			  scrollbar-width: none;     /* Firefox */
			  overflow-y: hidden;        /* Force vertical scroll */
			  overflow-x: hidden;        /* Force vertical scroll */
			}
			
			body-spots.noscroll::-webkit-scrollbar {
			  display: none;             /* Chrome, Safari */
			}
			.body-red {
				background: red;
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
				position: sticky;
				  top: 0;
				  background: #ddd;    /* or your table header color */
				  color: #000;
				  z-index: 0;
				  box-shadow: 0 2px 5px rgba(0,0,0,0.3);

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
				padding:1px; 
				border-left:1px solid black; 
				border-top:1px solid black;
				   text-align: center;
				background:white;				
			}
			th:nth-child(2),
			td:nth-child(2) {
			  width: 150px;
			}
			#tbody th,
			#tbody td {
			  overflow-wrap: break-word;
			  word-wrap: break-word;
			  white-space: normal;
			}
			#tbody th:nth-child(14),
			tbody td:nth-child(14) {
			  width: 150px;
			}
			#tbodylog {
			  overflow-x: hidden;
			  overflow-y: scroll;
			  height: 800px;
			}
			#tbody {
			  overflow-x: scroll;
			  overflow-y: scroll;
			  height: 800px;
			}
			@media screen and (max-width: 480px) {
			  #tbody {
			  overflow-x: scroll;
				overflow-y: scroll;
				height: 350px;
			  }
			
			  #tbodylog {
				overflow-x: scroll;
				  overflow-y: scroll;
				  height: 350px;
				}
			  
			}
			
			#tbodylog th,
			#tbodylog td {
			  overflow-wrap: break-word;
			  word-wrap: break-word;
			  white-space: normal;
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
			
			.full-viewport-height {
			  height: 100vh;
			}
			
			@supports (height: 100dvh) {
			  .full-viewport-height {
				height: 100dvh;
			  }
			}

			.striped td, .striped th {
			  height: 35px;
			  overflow: hidden;
			  vertical-align: middle;
			  white-space: nowrap;
			  text-overflow: ellipsis;
			  padding: 1px 1px;
			}
			#sp {
			  overflow-x: hidden;
			  overflow-y: hidden;
			  height: 800px;
			}
			
			#sp tr {
			  transition: opacity 0.5s ease;
			  opacity: 1;
			}
			
			#sp tr.faded {
			  opacity: 0.2;
			}

			
			#clusterTable {
			  overflow-y: scroll;
			  height: 800px;
			}
			
			.striped tr {
			  height: 35px;
			  cursor: pointer;
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
			#logt {
			  border: 1px solid #444;
			}
			
			#logt th, #logt td {
			  padding: 1px;
			  border: 1px solid #222;
			  text-align: left;
			}
			
			
			#sp thead th {
			  position: sticky;
			  top: 0;
			  background: #ddd;    /* or your table header color */
			  color: #000;
			  z-index: 0;
			  box-shadow: 0 2px 5px rgba(0,0,0,0.3);
			}

			.ibox {
				margin: 0 auto;
				margin-left: auto;
				margin-right: auto;}
			.input-group-text {
				width: 110px;
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
			
			td.selCk {
				text-align: center;
				justify-content: center !important;
				  padding: 20px;
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
				width: 80px !important;
			}
			
			.text-spacer{
				 margin-top:15px;
			}
			
			.fixed{
				width:320px;
			}
			@keyframes glow {
				  0% {
					box-shadow: 0 0 10px rgba(40, 167, 69, 1.0);
				  }
				  50% {
					box-shadow: 0 0 10px rgba(40, 167, 69, 1.0);
				  }
				  100% {
					box-shadow: 0 0 20px rgba(40, 167, 69, 1.0);
				  }
				}
				/* Hover effect on vertical thumb */
				/* When glow-on is active â€” add border + glow to wrapper */
					#sp.glow-on {
					box-shadow: 0 0 20px #444;
					border: 6px solid #0087ff;
					animation: glow 0.1s infinite ease-in-out;
				}
								
				#sp {
					box-shadow: 0 0 20px #444;
				  background-color: #444 !important;
					min-height: 20px;
					  border: 8px solid #444;
					  overflow-x: hidden;
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
			.modalDL-header{
				background-color: #e3f2fd;
				width:500px;	
			}
			#modalZ-title{
				color: red !important;
			background-color: #e8f8ff;
			text-align: left;
			vertical-align: middle;
			font-size: 30px;
			}
			#modalZ-header{
				color: red !important;
			background-color: #e8f8ff;
			text-align: left;
			vertical-align: middle;
			font-size: 30px;
			
			}
			#modalZ-body{
				background-color:white;
				width:490px;
				margin-left: 10px; 	
			}
			.modal-body{
				background-color:white;
				width:400px;	
			}
			.modalA-body{
				background-color:white;
				width:390px;
				padding-left:30px;
			}
			.modal-footer{
				background-color:lightgray;
				width:400px;	
			}
			
			.modal-dialog{
				background-color: #e3f2fd;
				width:400px;
				vertical-align: middle;	
			}
			.modalB-header{
				color: red !important;
				background-color: #e8f8ff;
				width:400px;
				text-align: center;
				vertical-align: middle;
				font-size: 30px;
			
			}
			.modalC-header{
				color: red !important;
				background-color: #e8f8ff;
				width:500px;
				text-align: center;
				vertical-align: middle;
				font-size: 20px;
				height: 50px;
				margin-top: 10px;
			}
			.modalB-body{
				text-align: center;
				background-color:white;
				width:350px;	
			}
			.modalC-body{
				text-align: center;
				background-color:white;
				width:350px;	
			}
			.modalB-footer{
				background-color:lightgray;
				width:400px;	
			}
			.modalC-footer{
				background-color:lightgray;
				width:500px;
				height: 50px;
				text-align:right;	
			}
			
			#pixFrame{
				background-color:white;
				width: 400px;
				max-width: 100%;
				  height: auto;
				  display: block;
				  margin: 20 auto;
			}
			
			.web-header{
				height: 200px; 
				background-color: #e3f2fd;	
			}
			
			.modal-title{
				margin-top: 0px; 
				background-color: #e3f2fd;
				width:400px;	
			}
			
			.modal-header{
				margin-top: 0px; 
				background-color: #e3f2fd;
				width:400px;	
			}
			.modalA-header{
				display: flex;
				justify-content: left;
				align-items: left;
				width: 100%;
				height: 50px;
				padding-left: 20px;
			}
			.modalA-content{
				color: red;
			}
			
			.modalA-title{
				background-color: #e3f2fd;
				display: flex;
				justify-content: left;
				align-items: left;
				width: 100%;
				height: 50px;
				font-size: 30px;
				padding-left: 20px;
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
				text-align:center;
				margin-left: 20px;

			}
			
			.web-body{
				background-color: #f3ffff;
				height: 100%;	
			}
			
			.flag{
				background-color: #f3ffff;
				text-align:center;	
			}

			.modal-pix{
				background-color: #f3ffff;	
				margin: 10 auto;
				display:block;
				height: auto;
				width:400px;
				border-width: 0px;
			}

			.thin {
				margin-top: 5px;
				margin-bottom: -2px;
			}
			
			.bs {
			  margin: 0;
			  overflow-x: scroll;
			  height:100%;
			}
			
			.sp {
			  margin: 0;
			  overflow-x: auto;
			  height:100%;
			}
			#bs {
			  overflow-y: scroll;
			  scrollbar-width: none; /* Firefox */
			  -ms-overflow-style: none;  /* Internet Explorer 10+ */
			}
			
			#bs::-webkit-scrollbar {
			  display: none; /* Safari and Chrome */
			}
			.striped tr {
			  transition: background-color 1s ease;
			}
			
			/* Striped rows */
			.striped tr:nth-child(even) {
			  background-color: #f0f0f0;
			}
			
			.striped tr:nth-child(odd) {
			  background-color: #00000;
			}
			
			/* Selection color applied via inline style */

		#bandCanvas {
				position:absolute;
				display:inline-block;
			}
			
			.PTTButton {
				font-size: 40px;
				position:absolute;
				width:100%;
				height:400px;
				border-radius: 10%;
				border: none;
			}
			
			.PTTButton-red {
				font-size: 40px;
				background: red;
				position:absolute;
				width:100%;
				height:400px;
				border-radius: 10%;
				border: none;
			}
			
			.BSbutton {
				position:absolute;
				left: 180px;
				outline: 2px solid black;
				right:10px;
				width:140px;
				height:30px;
				border-radius: 20% !important;
			}
			
			.BSFrequency{
				position:absolute;
				 left:20px;
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

			.active {
				color:#000 !important;
				background-color:#3399ff;
				background-image: none;
				background-repeat: no-repeat;
			}
			 .striped  {
				 border-collapse: separate; /* this is important */
				 border-spacing: 0;
			   }


			#tbody tr:hover td {
				 box-shadow: 0 2px 8px rgba(0, 135, 255, 0.4) !important;
				 background-color: rgba(0, 200, 200, 1.0) ;
				 transition: all 0.3s ease;
				 color:black;
				 position: relative;
				 z-index: 1;
			   }
			   #tbody tr:nth-child(odd) td {
				   cursor: pointer;
					background-color: #bae8f1;
				   max-height: 35px;
				   vertical-align: middle;
			   
					  text-overflow: ellipsis;
					  white-space: nowrap;
			   }
			   
			   #tbodyspots tr:nth-child(even) td {
				   cursor: pointer;
				   vertical-align: middle;
				   max-height: 35px;
					 text-overflow: ellipsis;
					 white-space: nowrap;
			   }
			   .striped tbody tr.highlight td {
					 cursor: pointer;
					 background-color: red !important;
					 font-weight: bolder;
					 font-size: larger;
				 }
				 
			   .table-striped tbody tr.highlight td {
					   cursor: pointer;
					   background-color: red !important;
					   font-weight: bolder;
					   font-size: larger;
				   }
				   

			#logt tr:hover td {
				 box-shadow: 0 2px 8px rgba(0, 135, 255, 0.4) !important;
				 background-color: rgba(0, 200, 200, 1.0) !important;
				 transition: all 0.3s ease;
				 color:black;
				 position: relative;
				 z-index: 1;
			   }
			   #logt tr:nth-child(odd) td {
				   cursor: pointer;
					background-color: #bae8f1;
				   max-height: 35px;
				   vertical-align: middle;
			   
					  text-overflow: ellipsis;
					  white-space: nowrap;
			   }
			   #logt tr:nth-child(even) td {
					  cursor: pointer;
					  vertical-align: middle;
					  max-height: 35px;
						text-overflow: ellipsis;
						white-space: nowrap;
				  }
				  
			   
			tbody tr.scrollCenterHighlight td {
				cursor: pointer;
				background-color: lightgreen;
			}

			.striped tbody tr.centerHighlight td {
			  background-color: yellow !important;
			  color:black !important;
			  cursor: pointer;
			}


			/*Bootstrap button outline override*/
			.btn-outline {
				color: white;
				transition: all .5s;
			}
			
			.btn-primary.btn-outline {
				color: white;
			}
			
			.dropdown-toggle.btn-outline {
				background-color: red !important;
				color: white;
				border: none;
				height: 80px;
			}
			.dropdown-toggle {
				background-color: black !important;
				color: white;
				border: none;
				height: 40px;
			}
			.dropdown-toggle:hover {
				background-color: #3d98f2 !important;
				color: white;
				border: none;
				height: 40px;
			}
			.btn-success.btn-outline {
				color: #5cb85c;
			}
			
			.btn-info.btn-outline {
				color: #5bc0de;
			}
			.btn-info:hover {
			border: none;
			color: black;
			height: 38px;
			outline: none;
			background-color: darkgray;
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
			.nav-link .active {
			  color: red;
			}
		.nav-item a:hover {
			background-color: #5bc0de !important;
			color: white !important;
			cursor: pointer;
		}
		.nav-item a:selected {
			background-color: black !important;
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
			overflow-x:hidden;
			  max-width: 100%;
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
#closeModal{
	color: black;
	background-color: #e3f2fd;
	display: flex;
	justify-content: right;
	align-items: right;
	width: 10%;
	height: 50px;
	font-size: 20px;
	padding-right: 20px;
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
		background-color: #5bc0de !important;
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
		height: 35px;
		margin-right: 10px;
	}
#sliderSpeed {
	height: 15px;
	margin-left: 0px;
	margin-right: 10px;
	border:none;
}
 #sliderSpeed .ui-slider-handle {
	 margin-left: -5px;
	 background: green;
	 border:none;
 }

#sliderAF {
	height: 15px;
	margin-left: 0px;
	margin-right: 10px;
	border:none;
}
 #sliderAF .ui-slider-handle {
	 background: green;
	 border:none;
 }
#sliderRF {
	height: 15px;
	margin-left: 0px;
	margin-right: 10px;
	border:none;
color:#000;
}
 #sliderRF .ui-slider-handle {
	 background: green;
	 border:none;
 }
#sliderPwrOut {
	height: 15px;
	margin-left: 0px;
	margin-right: 10px;
	border:none;
}
 #sliderPwrOut .ui-slider-handle {
	 background: green;
	 border:none;}
#sliderMic {
	height: 15px;
	margin-left: 0px;
	margin-right: 15px;
	border:none;
}
 #sliderMic .ui-slider-handle {
	 background: green;
	 border:none;
 }
 
 
</style>
