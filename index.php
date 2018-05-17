<?php
require_once("newFunctions.php");
require_once("Contact.php");
require_once("lib/tpl.php");
$cmd = isset($_GET["cmd"]) ? $_GET["cmd"] : "";
if (isset($_GET["error"])) $data['$error'] = $_GET["error"];

if ($cmd === "listPage") {
    print_json_data(read_all_contacts_sql());

} else if ($cmd === "addPage") { // KONTAKTIDE LISAMINE
    $input = json_decode(file_get_contents("php://input"));

    if ($input != null) {
        $errors = [];
        $phones = isset($input->phones) ? $input->phones : [];

        if (isset($input->firstName) == false or strlen($input->firstName) < 2) {
            $errors[] = "Eesnimi on liiga lühike või ei eksisteeri";
        }
        if (isset($input->lastName) == false or strlen($input->lastName) < 2) {
            $errors[] = "Perekonnanimi on liiga lühike või ei eksisteeri";
        }
        if (count($phones) === 0) {
            $errors[] = "Sisesta vähemalt üks telefoninumber";
        }

        if (count($errors) > 0) {
            $input->errors = $errors;
            print_json_data($input);
        } else {
            print_json_data(add_contact_sql(new Contact($input->firstName, $input->lastName, $phones)));
        }
    }
} else if ($cmd === "editContact") { // KONTAKTIDE MUUTMINE
    if(isset($_GET["contactId"])) {
        $id = $_GET["contactId"];
        $input = json_decode(file_get_contents("php://input"));

        if (get_contact_by_id2($id) == null) {
            $error = new stdClass();
            $error->error = "No such id in database";
            print_json_data($error);

        } else if ($input != null){

            $errors = [];
            $phones = isset($input->phones) ? $input->phones : [];

            if (isset($input->firstName) == false or strlen($input->firstName) < 2) {
                $errors[] = "Eesnimi on liiga lühike või ei eksisteeri";
            }
            if (isset($input->lastName) == false or strlen($input->lastName) < 2) {
                $errors[] = "Perekonnanimi on liiga lühike või ei eksisteeri";
            }
            if (count($phones) === 0) {
                $errors[] = "Sisesta vähemalt üks telefoninumber";
            }

            if (count($errors) > 0) {
                $input->errors = $errors;
                print_json_data($input);
            } else {
                print_json_data(edit_contact(new Contact($input->firstName, $input->lastName, $phones, $id)));
            }

        } else {
            print_json_data(get_contact_by_id2($id));
        }
    } else {
        $error = new stdClass();
        $error->error = "Parameter contactId was not set in the URL";
        print_json_data($error);
    }
} else { // ALGNE LEHT, EHK NIMEKIRJA VAADE
    readfile("templates/main2.html");
}
//$data['$contacts'] = read_all_contacts_sql();
//$data['$year'] = date('Y');
//print render_template("templates/main.html", $data);


function print_json_data($data) {
    header("Content-Type: application/json");
    print json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}