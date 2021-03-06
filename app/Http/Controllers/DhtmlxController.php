<?php

namespace iPMS\Http\Controllers;

use iPMS\User;
use iPMS\Project;
use iPMS\Schedule;
use iPMS\Resource;
use iPMS\GanttTask;
use iPMS\GanttLink;

use Dhtmlx\Connector\GridConnector;
use Dhtmlx\Connector\GanttConnector;
use Dhtmlx\Connector\SchedulerConnector;

class DhtmlxController extends Controller
{
	public function grid($tbl) {
		$conn = new GridConnector(null, "PHPLaravel");

		switch ($tbl) {
		case "users":
			//$model = new User();
			$model = User::where('id', '>', 0)->get();
			$data  = "uid,fullname,email,group";
			break;
		case "projects":
			$model = new Project();
			$data  = "ext,title,product,prj_group,plan_start,plan_end,start_date,end_date,version,status";
			break;
		case "schedules":
			$model = new Schedule();
			$data  = "uid,start_date,end_date,text";
			break;
		case "resources":
			$model = new Resource();
			$data  = "name,group,type,cost,unit,notes";
			break;
		}

		$conn->configure($model, "id", $data);
		$conn->render();
	}

	public function gantt($pid) {
		$conn = new GanttConnector(null, "PHPLaravel");
		$conn->render_links(new GanttLink($pid), "id", "source,target,type");
		$conn->render_table(new GanttTask($pid), "id",
			"start_date,duration,text,progress,type,parent");
	}

	public function schedule($pid) {
		$conn = new SchedulerConnector(null, "PHPLaravel");
		if ($pid == "prj") {
			$conn->mix("uid", "-1");
			$conn->configure(new Project(), "id", "start_date,end_date,title", false, false, "p");
		}
		else
			$conn->configure(new Schedule(), "id", "start_date,end_date,text,uid");
		$conn->render();
	}
}
