<?php
include '../db.php';

$login = $_GET["login"];
$result = DB::query("SELECT * FROM USER where login=?", [$login]);
if ($result->fetch()) {
    $response['status'] = "exist";
} else {
    $response['status'] = "not exist";
}
echo json_encode($response);
?>
