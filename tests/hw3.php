<?php

require_once('common.php');

const BASE_URL = 'http://localhost/icd0007';

class Hw3Tests extends HwTests {

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
        $this->assertField('phone');

        $this->assertField('submit-button');
    }

    function submittingFormAddsPersonToList() {

        $this->get(BASE_URL);

        $this->clickLinkById('add-page-link');

        $person = getSampleData();

        $this->setFieldByName('firstName', $person->firstName);
        $this->setFieldByName('lastName', $person->lastName);
        $this->setFieldByName('phone', $person->phone);

        $this->clickSubmitByName('submit-button');

        $this->assertText($person->firstName);
        $this->assertText($person->lastName);
        $this->assertText($person->phone);
    }

}

(new Hw3Tests())->run(new PointsReporter());
