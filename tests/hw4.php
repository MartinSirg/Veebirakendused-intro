<?php

require_once('common.php');

const BASE_URL = 'http://localhost/icd0007';

class Hw4Tests extends HwTests {

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

    function submittingFormAddsPersonToList() {

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
    }

    function makesRedirectAfterFormSubmission() {
        $this->get(BASE_URL);

        $this->clickLinkById('add-page-link');

        $this->setMaximumRedirects(0);

        $this->clickSubmitByName('submit-button');

        $this->assertResponse(302);
    }

}

(new Hw4Tests())->run(new PointsReporter());
