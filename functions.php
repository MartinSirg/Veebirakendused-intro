<?php

const FILE_NAME = "data.txt";

function add_contact($first, $last, $phone) {
    $toAppend = $first . ";" . $last . ";" . $phone . PHP_EOL;
    file_put_contents(FILE_NAME, $toAppend, FILE_APPEND);
}

function read_all_contacts() {
    $lines =  file(FILE_NAME, FILE_IGNORE_NEW_LINES);
    $contacts = [];
    foreach ($lines as $line) {
        $contacts[] = explode(";",$line);
    }
    return $contacts;
}
