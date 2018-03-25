<?php

require_once('common.php');

const BASE_URL = 'http://localhost/icd0007';

class Hw5Tests extends HwTests {

    function baseUrlResponds() {
        $this->assertTrue($this->get(BASE_URL));
        $this->assertResponse(200);
    }

    function listPageHasMenuWithCorrectLinks() {
        $this->get(BASE_URL);

        $this->assertLinkById('list-page-link');
        $this->assertLinkById('add-page-link');
    }

    function addPageHasCorrectElements() {
        $this->get(BASE_URL);

        $this->clickLinkById('add-page-link');

        $this->assertField('firstName');
        $this->assertField('lastName');
        $this->assertField('phone1');
        $this->assertField('phone2');
        $this->assertField('phone3');

        $this->assertField('submit-button');
    }

    function displaysErrorWhenSubmittingInvalidData() {

        $this->get(BASE_URL);

        $this->clickLinkById('add-page-link');

        $this->clickSubmitByName('submit-button');

        $this->assertPattern('/id\s*=\s*["\']error-block["\']/',
            "can't find element with id: 'error-block'");
    }

    function clickingOnPersonNameTakesToFilledEditForm() {
        $person = $this->insertPerson();

        $this->clickLink($person->firstName);

        $this->assertFieldByName('firstName', $person->firstName);
        $this->assertFieldByName('lastName', $person->lastName);
        $this->assertFieldByName('phone1', $person->phone1);
        $this->assertFieldByName('phone2', $person->phone2);
        $this->assertFieldByName('phone3', $person->phone3);
    }

    function supportsUpdatingExistingPersonData() {
        $person = $this->insertPerson();

        $this->clickLink($person->firstName);

        $updatedPhone = strrev($person->phone1);

        $this->setFieldByName('phone1', $updatedPhone);

        $this->clickSubmitByName('submit-button');

        $this->assertText($updatedPhone);
        $this->assertNoText($person->phone1);
    }

    private function insertPerson() {
        $this->get(BASE_URL);

        $this->clickLinkById('add-page-link');

        $person = getSampleData();

        $this->setFieldByName('firstName', $person->firstName);
        $this->setFieldByName('lastName', $person->lastName);
        $this->setFieldByName('phone1', $person->phone1);
        $this->setFieldByName('phone2', $person->phone2);
        $this->setFieldByName('phone3', $person->phone3);

        $this->clickSubmitByName('submit-button');

        $this->assertText($person->firstName);
        $this->assertText($person->lastName);
        $this->assertText($person->phone1);
        $this->assertText($person->phone2);
        $this->assertText($person->phone3);

        return $person;
    }
}

(new Hw5Tests())->run(new PointsReporter());

