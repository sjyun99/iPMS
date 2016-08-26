@extends('layouts.master')

@section('library')
<script src="/js/dhtmlxscheduler.js"></script>
<script src="/js/dhtmlxscheduler_year_view.js"></script>
<script src="/js/dhtmlxscheduler_container_autoresize.js"></script>
<script src="/js/dhtmlxscheduler_minical.js"></script>
<script src="/js/dhtmlxscheduler_readonly.js"></script>
<script src="/js/dhtmlxscheduler_multisource.js"></script>
<link rel="stylesheet" href="/css/dhtmlxscheduler.css">
@stop

@section('content')
@include('layouts.menubar')
<h1 class="page-header">Project Calendar</h1>

<div id="scheduler" class="dhx_cal_container" style="width:100%; height:100%">
	<div class="dhx_cal_navline">
		<div class="dhx_cal_prev_button">&nbsp;</div>
		<div class="dhx_cal_next_button">&nbsp;</div>
		<div class="dhx_cal_today_button"></div>
		<div class="dhx_cal_date"></div>
		<div class="dhx_cal_tab" name="day_tab"></div>
		<div class="dhx_cal_tab" name="week_tab"></div>
		<div class="dhx_cal_tab" name="month_tab""></div>
		<div class="dhx_cal_tab" name="year_tab""></div>
	</div>
	<div class="dhx_cal_header"></div>
	<div class="dhx_cal_data"></div>
</div>
<div style="margin-top:10px">
	<button class="btn-xs btn-danger" style="float:right" onclick="dp.sendData()"><span class="glyphicon glyphicon-save"></span> Update</button>
</div>

<style>
	.weekend .dhx_month_body {background: #f4f7f4 !important;}
	.weekend .dhx_month_head {color:red; background: #f4f7f4 !important;}
	.dhx_cal_event.event_holiday div,
	.dhx_cal_event_line.event_holiday {background:red !important}
	.dhx_cal_event_clear.event_holiday {color:red !important}
	.dhx_cal_event.event_project div,
	.dhx_cal_event_line.event_project {background:blue !important}
	.dhx_cal_event_clear.event_project {color:blue !important}
</style>
<script>
	scheduler.config.xml_date = "%Y-%m-%d %H:%i";
	scheduler.config.api_date = "%Y-%m-%d %H:%i";
	scheduler.config.default_date = "%Y.%m.%d";
	scheduler.config.month_date = "%Y. %m월";
	scheduler.config.day_date = "%D (%m.%d)";
	scheduler.config.start_on_monday = false;
	scheduler.config.first_hour = 8;
	scheduler.config.last_hour = 20;
	scheduler.config.time_step = 30;
	scheduler.config.limit_time_select = true;
	scheduler.config.full_day = true;

	scheduler.xy.menu_width = 0;
	scheduler.config.details_on_dblclick = true;
	scheduler.config.details_on_create = true;
	scheduler.attachEvent("onClick", function(){return false;});

	scheduler.config.lightbox.sections = [
		{ name:"description", height:30, map_to:"text", type:"textarea", focus:true },
		{ name:"time", height:72, type:"calendar_time", map_to:"auto" }
	];

	scheduler.templates.month_date_class = function(date, today) {
		if (date.getDay()==0 || date.getDay()==6) return "weekend";
	}
	scheduler.templates.event_class = function(start, end, event) {
		if (event.uid == "0")  return "event_holiday";
		if (event.uid == "-1") return "event_project";
	}

	scheduler.filter_month = scheduler.filter_week =
	scheduler.filter_day = scheduler.filter_year = function(id, event) {
		if ({{ Auth::user()->group }} == 0 ||
			Number(event.uid) <= 0 || event.uid == {{ Auth::user()->id }}) return true;
		return false;
	}
	scheduler.attachEvent("onEventSave", function(id, event, is_new) {
		if (!event.text) {
			alert ("스케쥴 내용이 비어 있습니다.")
			return false;
		}
		if (is_new) event.uid = {{ Auth::user()->id }};
		return true;
	});

	scheduler.attachEvent("onEventLoading", function(event) {
		if (Number(event.uid) <= 0) event.readonly = true;
		return true;
	});
	scheduler.attachEvent("onBeforeDrag", function(id) {
		if (!id) return true;
		return !this.getEvent(id).readonly;
	});

	scheduler.init('scheduler', false, "month");
	scheduler.load(["/schedule_/1", "/schedule_/prj"]);

	var dp = new dataProcessor("/schedule_/1");
	dp.init(scheduler);
	dp.setUpdateMode("off");
</script>
@stop