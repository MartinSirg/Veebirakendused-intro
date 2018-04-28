<?php
require_once("newFunctions.php");
require_once("Contact.php");
require_once("lib/tpl.php");
$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "listPage";
$data = [];
if (isset($_GET["error"])) $data['$error'] = $_GET["error"];

if ($cmd === "listPage") {
    $data['$selector'] = 1;
} else if ($cmd === "addPage") {
    $data['$selector'] = 0;
} else if ($cmd === "contactAdded") {
//----------------------------------------INPUTI CONTROLLIMINE----------------------------------------------------------
//    if (strlen($_POST["firstName"]) < 3 or strlen($_POST["firstName"]) > 15 ) {
//        header('Location: ?cmd=addPage&error=Eesnimi+peab+olema+3+ja+15+tähemärgi+vahel');
//    } elseif (strlen($_POST["lastName"]) < 3 or strlen($_POST["lastName"]) > 15 ) {
//        header('Location: ?cmd=addPage&error=Perekonnanimi+peab+olema+3+ja+15+tähemärgi+vahel');
//    } elseif (strlen($_POST["phone1"]) > 15 or strlen($_POST["phone2"]) > 15 or strlen($_POST["phone3"]) > 15) {
//        header('Location: ?cmd=addPage&error=Telefoni+number+pikem+kui+15+tähemärki');
//    } else {
//        $phones["phone1"] = $_POST["phone1"];
//        $phones["phone2"] = $_POST["phone2"];
//        $phones["phone3"] = $_POST["phone3"];
//        $contact = new Contact($_POST["firstName"], $_POST["lastName"], $phones);
//        add_contact_sql($contact);
//        header('Location: ?cmd=listPage');
//        return;
//    }
//----------------------------------------------------------------------------------------------------------------------
    $phones["phone1"] = $_POST["phone1"];
    $phones["phone2"] = $_POST["phone2"];
    $phones["phone3"] = $_POST["phone3"];
    $contact = new Contact($_POST["firstName"], $_POST["lastName"], $phones);
    add_contact_sql($contact);
    header('Location: ?cmd=listPage');
    return;
} else {
    header('Location: ?cmd=listPage');
}
$data['$contacts'] = read_all_contacts_sql();
$data['$year'] = date('Y');
print render_template("templates/main.html", $data);
