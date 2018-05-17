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
        if ($num != "") {
            $statement = $connection->prepare("insert into phones (id, phoneNum) values ($id, :phone)");
            $statement-> bindValue(":phone", $num);
            $statement->execute();
        }
    }
    return get_contact_by_id2($id);
}

function edit_contact($contact) {
    $connection = new PDO("sqlite:data.sqlite");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $connection->prepare("UPDATE contact SET firstName = :firstName, lastName = :lastName WHERE id = :id");
    $statement->bindValue(":firstName", $contact->firstName);
    $statement->bindValue(":lastName", $contact->lastName);
    $statement->bindValue(":id", $contact->id);
    $statement->execute();

    $statement2 = $connection->prepare("DELETE FROM phones WHERE id = :id");
    $statement2->bindValue(":id", $contact->id);
    $statement2->execute();

    foreach ($contact->phones as $phone) {
        if ($phone != "") {
            $statement3 = $connection->prepare("INSERT INTO phones (id, phoneNum) VALUES (:id, :phone)");
            $statement3->bindValue(":id", $contact->id);
            $statement3->bindValue(":phone", $phone);
            $statement3->execute();
        }
    }
    return get_contact_by_id2($contact->id);
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

function get_contact_by_id($id) {
    $connection = new PDO("sqlite:data.sqlite");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $connection->prepare("select contact.id, contact.firstName, contact.lastName, phones.phoneNum
      from contact LEFT JOIN phones ON contact.id = phones.id WHERE phones.id = :id");
    $statement->bindValue(":id", $id);
    $statement->execute();
    $contact = null;

    foreach ($statement as $row) {
        if ($contact != null) {
            $contact->phones[] = $row["phoneNum"];
        } else {
            $phones = [];
            $phones[] = $row["phoneNum"];
            $contact = new Contact($row["firstName"], $row["lastName"], $phones, $row["id"]);
        }
    }
    return $contact;
}
function get_contact_by_id2($id) {
    $contacts = read_all_contacts_sql();
    foreach ($contacts as $contact) {
        if ($contact->id == $id) return $contact;
    }
}
