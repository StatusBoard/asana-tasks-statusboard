<?php

// Define Asana API Key
define('ASANA_API_KEY', 'REPLACE THIS');
define('ASANA_WORKSPACE_ID', 'REPLACE THIS');

// Include Asana API Class
require_once('asana.php');

// Initialize Asana API Class
$asana = new Asana(ASANA_API_KEY);

$graphTodo = array();
$graphDone = array();
$errors = array(
	'message' => '',
	'detail'  => ''
);
$users = json_decode($asana->getUsers());

if (property_exists($users, 'data') && is_array($users->data)) {
	foreach ($users->data as $u) {
		$todo    = 0;
		$done    = 0;
		$due     = 0;
		$filters = array("assignee" => $u->id, "workspace" => ASANA_WORKSPACE_ID);
		$tasks   = json_decode($asana->getTasksByFilter($filters, 'completed,due_on'));
		
		if (property_exists($tasks, 'data') && is_array($tasks->data)) {
			foreach ($tasks->data as $t) {
				$dueDate = new DateTime($t->due_on);
				$nowDate = new DateTime('now');
				if (!$t->completed && $dueDate < $nowDate) {
					$due++;
				}
				if ($t->completed) {
					$done++;
				}
				if (!$t->completed && $dueDate > $nowDate) {
					$todo++;
				}
			}
		}
		else {
			$errors = array(
				'message' => 'No tasks',
				'detail'  => 'We couldnt find any tasks.'
			);
		}
		$name = explode(' ', $u->name);
		$graphPast[] = array('title' => $name[0], 'value' => $due);
		$graphTodo[] = array('title' => $name[0], 'value' => $todo);
		$graphDone[] = array('title' => $name[0], 'value' => $done);
	}
}
else {
	$errors = array(
		'message' => 'No users',
		'detail'  => 'We couldnt find any users.'
	);
}

$graph = array(
	"graph" => array(
		"title" => "Asana Tasks Per Person",
		"type"  => "bar",
		"total" => true,
		"refreshEveryNSeconds" => 120,
		"xAxis" => array(
			"showEveryLabel" => true
		),
		"datasequences" => array(
			array(
				"title"      => "Past Due",
				"datapoints" => $graphPast,
				"color"      => "orange"
			),
			array(
				"title"      => "Todo",
				"datapoints" => $graphTodo,
				"color"      => "yellow"
			),
			array(
				"title"      => "Done",
				"datapoints" => $graphDone,
				"color"      => "green"
			)
		)
	)
);
if ($errors['message'] !== '' || $errors['detail'] !== '') {
	$graph["graph"]["error"] = $errors;
}
// You can turn on JSON_PRETTY_PRINT if you're running PHP v5.4
header('Content-Type: application/json');
exit(json_encode($graph/*, JSON_PRETTY_PRINT*/));