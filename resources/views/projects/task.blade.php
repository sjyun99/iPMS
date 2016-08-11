@extends('layouts.master')

@section('library')
<script src="/js/dhtmlxgantt.js"></script>
<script src="/js/dhtmlxgantt_fullscreen.js"></script>
<script src="/js/dhtmlxgantt_auto_scheduling.js"></script>
<script src="/js/dhtmlxgantt_marker.js"></script>
<link rel="stylesheet" href="/css/dhtmlxgantt.css">
<link rel="stylesheet" href="/css/font-awesome.min.css">
@stop

@section('content')
@include('layouts.menubar')
<h1 class="page-header">Project Task</h1>

<label class="radio-inline"><input type="radio" name="scale" onclick="setScale('day')" checked>Day</label>
<label class="radio-inline"><input type="radio" name="scale" onclick="setScale('month')">Month</label>
<label class="radio-inline"><input type="radio" name="scale" onclick="setScale('year')">Year</label>

<div id="gantt" style='width:100%; height:450px'></div>

<style>
	.gantt-fullscreen{
		position: absolute;
		bottom: 20px;
		right: 20px;
		width: 24px;
		height: 24px;
		padding: 2px;
		font-size: 24px;
		background: transparent;
		cursor: pointer;
		opacity: 0.5;
		text-align: center;
		-webkit-transition:background-color 0.5s, opacity 0.5s;
		transition:background-color 0.5s, opacity 0.5s;
	}
	.gantt-fullscreen:hover{
		background: rgba(150, 150, 150, 0.5);
		opacity: 1;
	}
	.gantt_task_line.gantt_dependent_task {
		background-color: #65c16f;
		border: 1px solid #3c9445;
	}
	.gantt_task_line.gantt_dependent_task .gantt_task_progress {
		background-color: #46ad51;
	}

	.custom-project {
		position: absolute;
		height: 6px;
		color: #ffffff;
		background-color: #444444;
	}
	.custom-project div {position: absolute;}
	.project-left, .project-right {
		top: 6px;
		background-color: transparent;
		border-style: solid;
		width: 0px;
		height: 0px;
	}
	.project-left {
		left:0px;
		border-width: 0px 0px  8px 7px;
		border-top-color: transparent;
		border-right-color: transparent !important;
		border-bottom-color: transparent !important;
		border-left-color: #444444 !important;
	}
	.project-right {
		right:0px;
		border-width: 0px 7px 8px 0px;
		border-top-color: transparent;
		border-right-color: #444444;
		border-bottom-color: transparent !important;
		border-left-color: transparent;
	}
	.project-line {font-weight: bold;}
	.gantt_task_line, .gantt_line_wrapper div {
		background-color: blue;
		border-color: blue;
		border-radius: 1;
	}
	.gantt_link_arrow {border-color: blue;}
	.gantt_task_link:hover .gantt_line_wrapper div {
		box-shadow: 0 0 5px 0px #9fa6ff;
	}
	.gantt_task_line .gantt_task_progress{
		opacity: 0.3;
		background-color: #444444;
	}
	.gantt_grid_data .gantt_cell {border-right: 1px solid #ececec;}
	.gantt_grid_data .gantt_cell.gantt_last_cell {border-right: none;}
	.gantt_tree_icon.gantt_folder_open,
	.gantt_tree_icon.gantt_file,
	.gantt_tree_icon.gantt_folder_closed {
		display: none;
	}
	.gantt_task .gantt_task_scale .gantt_scale_cell,
	.gantt_grid_scale .gantt_grid_head_cell {color:#5c5c5c;}
	.gantt_row, .gantt_cell {border-color:#cecece;}
	.gantt_grid_scale .gantt_grid_head_cell {
		border-right: 1px solid #cecece !important;
	}
	.gantt_grid_scale .gantt_grid_head_cell.gantt_last_cell  {
		border-right: none !important;
	}

	.weekend {background: #f4f7f4 !important;}
	.gantt_selected .weekend {background:#FFF3A1 !important;}
</style>
<script>
	function setScale(val) {
		setConfigScale(val);
		gantt.render();
	}

	function setConfigScale(val) {
		switch (val) {
		case "day":
			gantt.config.scale_unit = "day";
			gantt.config.date_scale = "%d";
			gantt.config.subscales = [
				{ unit:"month", step:1, date:"%Y. %m월" }
			];
			gantt.config.min_column_width = 22;
			gantt.templates.scale_cell_class = function(date) {
				if (date.getDay()==0 || date.getDay()==6) { return "weekend"; }
			};
			gantt.templates.task_cell_class = function(item, date) {
				if (date.getDay()==0 || date.getDay()==6) { return "weekend"; }
			};
			break;
		case "month":
			gantt.config.scale_unit = "month";
			gantt.config.date_scale = "%Y. %m월";
			gantt.config.subscales = [
				{ unit:"week", step:1, date:"%W주" }
			];
			gantt.config.min_column_width = 50;
			gantt.templates.scale_cell_class = function(date) { return null; }
			break;
		case "year":
			gantt.config.scale_unit = "year";
			gantt.config.date_scale = "%Y";
			gantt.config.subscales = [
				{ unit:"month", step:1, date:"%m월" }
			];
			gantt.config.min_column_width = 60;
			gantt.templates.task_cell_class = function(item, date) { return null; }
			break;
		}
	}

	gantt.config.row_height = 30;
	gantt.config.task_height = 16;
	gantt.config.keep_grid_width = false;
	gantt.config.grid_resize = true;

	gantt.config.xml_date = "%Y-%m-%d %H:%i:%s";
	gantt.config.step = 1;
	//gantt.config.work_time = true;	// 월화수목금금금
	gantt.config.order_branch = true;
	gantt.config.auto_scheduling = true;
	gantt.config.auto_scheduling_strict = true;
	gantt.config.drag_progress = false;
	setConfigScale("day");	// default day scale

	gantt.config.columns = [
		{name:"text",       label:"Task",  width:"*", tree:true },
		{name:"start_date", label:"Start", width:80,  align:"center" },
		{name:"end_date",   label:"End",   width:80,  align:"center" },
		{name:"duration",   label:"Day",   width:48,  align:"center" },
		{name:"add",        label:"",      width:38 }
	];
	gantt.config.grid_width = 400;
	gantt.config.grid_resize = true;
	//gantt.config.min_grid_column_width = 70;

	gantt.locale.labels["section_progress"] = "Progress";
	gantt.config.lightbox.sections = [
		{name: "description", type: "textarea", map_to: "text", height: 28, focus: true},
		{name: "type", type: "typeselect", map_to: "type"},
		{name: "progress", type: "select", map_to: "progress", options: [
			{key:"0", label: "Not started"},
			{key:"0.1", label: "10%"},
			{key:"0.2", label: "20%"},
			{key:"0.3", label: "30%"},
			{key:"0.4", label: "40%"},
			{key:"0.5", label: "50%"},
			{key:"0.6", label: "60%"},
			{key:"0.7", label: "70%"},
			{key:"0.8", label: "80%"},
			{key:"0.9", label: "90%"},
			{key:"1", label: "Complete"} ]},
		{name: "time", type: "duration", map_to: "auto", time_format:["%Y", "%m", "%d"]}
	];

	var today = new Date();
	gantt.addMarker({start_date: today, css: "today", text: "Today"});

	// classic look
	gantt.config.type_renderers[gantt.config.types.project] = function(task) {
		var main_el = document.createElement("div");
		main_el.setAttribute(gantt.config.task_attribute, task.id);
		var size = gantt.getTaskPosition(task);
		main_el.innerHTML = [
			"<div class='project-left'></div>",
			"<div class='project-right'></div>"
		].join('');
		main_el.className = "custom-project";
		main_el.style.left = size.left + "px";
		main_el.style.top = size.top + 7 + "px";
		main_el.style.width = size.width + "px";
		return main_el;
	};

	gantt.templates.grid_row_class = function(start, end, task) {
		if (task.type == gantt.config.types.project) return "project-line";
	};
	gantt.templates.rightside_text = function(start, end, task)  {
		return (task.type == gantt.config.types.milestone) ? task.text : "";
	};

	(function () {	// Fullscreen Expand / Collapse
		gantt.attachEvent("onTemplatesReady", function(){
			var toggle = document.createElement("i");
			toggle.className = "fa fa-expand gantt-fullscreen";
			gantt.toggleIcon = toggle;
			gantt.$container.appendChild(toggle);
			toggle.onclick = function() {
				var mbar = document.getElementById("menubar");
				if (!gantt.getState().fullscreen) {
					mbar.style.visibility = "hidden";
					gantt.expand();
				}
				else {
					mbar.style.visibility = "visible";
					gantt.collapse();
				}
			};
		});
		gantt.attachEvent("onExpand", function() {
			var icon = gantt.toggleIcon;
			if (icon) icon.className = icon.className.replace("fa-expand", "fa-compress");
		});
		gantt.attachEvent("onCollapse", function() {
			var icon = gantt.toggleIcon;
			if (icon) icon.className = icon.className.replace("fa-compress", "fa-expand");
		});
	})();

	(function () {	// Tasks with Subtasks automatically become Projects
		var delTaskParent;
		function checkParents(id) {
			setTaskType(id);
			var parent = gantt.getParent(id);
			if (parent != gantt.config.root_id) checkParents(parent);
		}
		function setTaskType(id) {
			id = id.id ? id.id : id;
			var task = gantt.getTask(id);
			//var type = gantt.hasChild(task.id) ? gantt.config.types.project : gantt.config.types.task;
			var type = gantt.hasChild(task.id) ? gantt.config.types.project : task.type;
			if (type != task.type) {
				task.type = type;
				gantt.updateTask(id);
			}
		}
		gantt.attachEvent("onParse", function() {
			gantt.eachTask(function(task) { setTaskType(task); });
		});
		gantt.attachEvent("onAfterTaskAdd", function onAfterTaskAdd(id) {
			gantt.batchUpdate(checkParents(id));
		});
		gantt.attachEvent("onBeforeTaskDelete", function onBeforeTaskDelete(id, task) {
			delTaskParent = gantt.getParent(id);
			return true;
		});
		gantt.attachEvent("onAfterTaskDelete", function onAfterTaskDelete(id, task) {
			if (delTaskParent != gantt.config.root_id)
				gantt.batchUpdate(checkParents(delTaskParent));
		});
	})();

	gantt.init("gantt");

var tasks = {
	"data":[
		{"id":11, "text":"Project #1", "start_date":"", "duration":"", "progress": 0.6, "open": true, type:gantt.config.types.project},
		{"id":12, "text":"Task #1", "start_date":"2016-08-03", "duration":"5", "parent":"11", "progress": 1, "open": true},
		{"id":13, "text":"Task #2", "start_date":"", "duration":"", "parent":"11", "progress": 0.5, "open": true},
		{"id":14, "text":"Task #3", "start_date":"2016-08-02", "duration":"6", "parent":"11", "progress": 0.8, "open": true},
		{"id":15, "text":"Task #4", "start_date":"", "duration":"", "parent":"11", "progress": 0.2, "open": true},
		{"id":16, "text":"Task #5", "start_date":"2016-08-12", "duration":"17", "parent":"11", "progress": 0, "open": true},

		{"id":17, "text":"Task #2.1", "start_date":"2016-08-03", "duration":"8", "parent":"13", "progress": 1, "open": true},
		{"id":18, "text":"Task #2.2", "start_date":"2016-08-06", "duration":"20", "parent":"13", "progress": 0.8, "open": true},
		{"id":19, "text":"Task #2.3", "start_date":"2016-08-10", "duration":"14", "parent":"13", "progress": 0.2, "open": true},
		{"id":20, "text":"Task #2.4", "start_date":"2016-08-10", "duration":"18", "parent":"13", "progress": 0, "open": true},
		{"id":21, "text":"Task #4.1", "start_date":"2016-10-03", "duration":"14", "parent":"15", "progress": 0.5, "open": true},
		{"id":22, "text":"Task #4.2", "start_date":"2016-10-03", "duration":"16", "parent":"15", "progress": 0.1, "open": true},
		{"id":23, "text":"Task #4.3", "start_date":"2016-10-03", "duration":"18", "parent":"15", "progress": 0, "open": true},
		{"id":24, "text":"Mielstone #5", "start_date":"2016-08-29", "duration":"0", "parent":"11", type:gantt.config.types.milestone},
	],
	"links":[
		{"id":"10","source":"11","target":"12","type":"1"},
		{"id":"11","source":"11","target":"13","type":"1"},
		{"id":"12","source":"11","target":"14","type":"1"},
		{"id":"13","source":"11","target":"15","type":"1"},
		{"id":"14","source":"11","target":"16","type":"1"},
		{"id":"15","source":"13","target":"17","type":"1"},
		{"id":"16","source":"17","target":"18","type":"0"},
		{"id":"17","source":"18","target":"19","type":"0"},
		{"id":"18","source":"19","target":"20","type":"0"},
		{"id":"19","source":"15","target":"21","type":"2"},
		{"id":"20","source":"15","target":"22","type":"2"},
		{"id":"21","source":"15","target":"23","type":"2"},
		{"id":"22","source":"16","target":"24","type":"0"},
	]
};
	gantt.parse(tasks);

	// refers to the 'data' action that we will create in the next substep
	//gantt.load("data.xml", "xml");
	//gantt.load("data.json", "json");

	// refers to the 'data' action as well
	//var dp = new gantt.dataProcessor("./gantt_data");
	//dp.init(gantt);
</script>
@stop
