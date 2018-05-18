<?php
require_once("newFunctions.php");
require_once("Contact.php");
require_once("lib/tpl.php");
$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "listPage";
$data = [];

if ($cmd === "listPage") {
    $data['$displayContacts'] = 1;
    if (isset($_GET["error"])) {
        $data['$displayErrors'] = 1;
        if ($_GET["error"] === "1") $data['$errors'] = ["Isikut ei leitud andmebaasist"];
        if ($_GET["error"] === "2") $data['$errors'] = ["Isiku ID parameetrit ei leitud URList"];
    }
} else if ($cmd === "addPage") {
    $data['$displayAddPage'] = 1;

    if (isset($_POST["firstName"]) and isset($_POST["lastName"]) and isset($_POST["phone1"])
        and isset($_POST["phone1"]) and isset($_POST["phone1"]) ) {
        $errors = [];

        if (strlen($_POST["firstName"]) < 2) {
            $errors[] = "Eesnimi on liiga lühike.";
        }
        if (strlen($_POST["lastName"]) < 2) {
            $errors[] = "Perekonnanimi on liiga lühike";
        }
        if (count($errors) > 0) {
            $data['$displayErrors'] = 1;
            $data['$errors'] = $errors;
            $data['$input'] = [$_POST["firstName"], $_POST["lastName"], $_POST["phone1"],$_POST["phone2"], $_POST["phone3"]];

        } else {
            $phones = [];
            $phones["phone1"] = $_POST["phone1"];
            $phones["phone2"] = $_POST["phone2"];
            $phones["phone3"] = $_POST["phone3"];
            $contact = new Contact($_POST["firstName"], $_POST["lastName"], $phones);
            add_contact_sql($contact);
            header('Location: ?cmd=listPage');
        }
    } else {
        $input = ["", "", "", "", ""];
        $data['$input'] = $input;
    }
} else if ($cmd === "editPage") {
    $data['$displayEditPage'] = 1;
    if (isset($_GET["contactID"])) { // If contactID is set as GET parameter
        $person = get_contact_by_id($_GET["contactID"]);
        if ($person != null) { //If person exists in database
            if (isset($_POST["firstName"]) and isset($_POST["lastName"]) and isset($_POST["phone1"])
                and isset($_POST["phone1"]) and isset($_POST["phone1"]) ) { // Receiving post request for editing
                $errors = [];

                if (strlen($_POST["firstName"]) < 2) {
                    $errors[] = "Eesnimi on liiga lühike.";
                }
                if (strlen($_POST["lastName"]) < 2) {
                    $errors[] = "Perekonnanimi on liiga lühike";
                }
                if (count($errors) > 0) {
                    $data['$displayErrors'] = 1;
                    $data['$errors'] = $errors;
                    $data['$input'] = [$_POST["firstName"], $_POST["lastName"], $_POST["phone1"],$_POST["phone2"], $_POST["phone3"], $_GET["contactID"]];

                } else { //if didn't find errors - change contact and POST-REDIRECT-GET
                    $phones = [];
                    $phones["phone1"] = $_POST["phone1"];
                    $phones["phone2"] = $_POST["phone2"];
                    $phones["phone3"] = $_POST["phone3"];
                    $contact = new Contact($_POST["firstName"], $_POST["lastName"], $phones, $_GET["contactID"]);
                    edit_contact($contact);
                    header('Location: ?cmd=listPage');
                }
            } else { // Receiving get request - just display contact info in textfields
                $input = [$person->firstName, $person->lastName];
                $input[] = count($person->phones) >= 1 ? $person->phones[0] : "";
                $input[] = count($person->phones) >= 2 ? $person->phones[1] : "";
                $input[] = count($person->phones) >= 3 ? $person->phones[2] : "";
                $input[] = $_GET["contactID"];
                $data['$input'] = $input;
            }
        } else { // If person is not in database
            header('Location: ?cmd=listPage&error=1');
        }
    }
    else { // If contactID GET parameter is set
        header('Location: ?cmd=listPage&error=2');
    }
}

else {
    header('Location: ?cmd=listPage');
}
$data['$contacts'] = read_all_contacts_sql();
$data['$year'] = date('Y');
print render_template("templates/main.html", $data);
