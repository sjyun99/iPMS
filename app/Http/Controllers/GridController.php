<?php

namespace iPMS\Http\Controllers;

use iPMS\User;
use iPMS\Project;
use Dhtmlx\Connector\GridConnector;

class GridController extends Controller
{
	public function data($tbl) {
		switch ($tbl) {
		case "users":
			//$model = new User;
			$model = User::where('id', '>', 0)->get();
			$data  = "uid, fullname, email";
			break;
		case "projects":
			$model = new Project;
			$data  = "ext, title, product, start_date, due_date, version, status";
			break;
		}

		$connector = new GridConnector(null, "PHPLaravel");
		$connector->configure($model, "id", $data);
		$connector->render();
	}
}