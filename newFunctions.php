<?php

function add_contact_sql($contact) {

    $connection = new PDO("sqlite:data.sqlite");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $connection->prepare("insert into contact (firstName, lastName) values (:firstName, :lastName)");
    $statement-> bindValue(":firstName", $contact->firstName);
    $statement-> bindValue(":lastName", $contact->lastName);
    $statement->execute();

    $id = $connection->lastInsertId();
    foreach ($contact->phones as $num ) {
        $statement = $connection->prepare("insert into phones (id, phoneNum) values ($id, :phone)");
        $statement-> bindValue(":phone", $num);
        $statement->execute();
    }
}

function read_all_contacts_sql() {
    $connection = new PDO("sqlite:data.sqlite");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statement = $connection->prepare("select contact.id, contact.firstName, contact.lastName, phones.phoneNum from contact LEFT JOIN phones ON contact.id = phones.id");
    $statement->execute();

    $contacts = [];
    $recurringIds = [];


    foreach ($statement as $row) {
        if (in_array($row["id"], $recurringIds)) {
            foreach ($contacts as $contact) {
                if ($contact->id === $row["id"]) {
                    $contact->phones[] = $row["phoneNum"];
                }
            }
        } else {
            $phones = [];
            $phones[] = $row["phoneNum"];
            $contacts[] = new Contact($row["firstName"], $row["lastName"], $phones, $row["id"]);
            $recurringIds[] = $row["id"];
        }
    }
    return $contacts;
}
