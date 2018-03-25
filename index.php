<?php
require_once("functions.php");
require_once("lib/tpl.php");
$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "listPage";
$data = [];

if ($cmd === "listPage") {
    $data['$selector'] = 1;
} else if ($cmd === "addPage") {
    $data['$selector'] = 0;
} else if ($cmd === "contactAdded") {
    $data['$selector'] = 1;
    add_contact($_POST["firstName"], $_POST["lastName"], $_POST["phone"]);
}
$data['$contacts'] = read_all_contacts();
$data['$year'] = date('Y');
print render_template("templates/main.html", $data);
