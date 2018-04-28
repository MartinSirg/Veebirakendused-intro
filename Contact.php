<?php

class Contact{
    public $firstName;
    public $lastName;
    public $phones;
    public $id;

    function __construct($firstName, $lastName, $phones, $id = null){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phones = $phones;
        $this->id = $id;
    }

}