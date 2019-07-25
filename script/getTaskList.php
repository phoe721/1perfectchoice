<?
require_once("class/task.php");
$task = new task();
echo json_encode($task->get_menu());
?>
