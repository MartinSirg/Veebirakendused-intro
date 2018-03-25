<?php

require_once(__DIR__ . '/vendor/simpletest/simpletest/unit_tester.php');
require_once(__DIR__ . '/vendor/simpletest/simpletest/web_tester.php');

const RESULT_PATTERN = "\nRESULT: %s POINTS\n";

class HwTests extends WebTestCase {

    public function getTests() {
        $class = get_class($this);

        $r = new ReflectionClass($class);

        $testMethods = array_filter($r->getMethods(), function ($each) use ($class) {
            return $each->class === $class && $each->isPublic();
        });

        $methodNames = array_map(
            function ($each) {
                return $each->name;
            }, $testMethods);

        $selected = array_filter($methodNames, function ($each) {
            return preg_match('/^_/', $each);
        });

        if ($selected) {
            return $selected;
        }

        return $methodNames;
    }
}

class PointsReporter extends TextReporter {
    private $points = 10;

    public function paintFooter($test_name) {
        printf(RESULT_PATTERN , $this->points);
    }

    public function paintFail($message) {
        $this->points = 0;

        parent::paintFail($message);
    }
}

class Person {
    public $firstName;
    public $lastName;
    public $phone;
    public $phone1;
    public $phone2;
    public $phone3;
}

function getSampleData() {
    $person = new Person();
    $randomValue = substr(md5(mt_rand()), 0, 9);
    $person->firstName = $randomValue . '0';
    $person->lastName = $randomValue . '1';
    $person->phone = $randomValue . '2';
    $person->phone1 = $randomValue . '3';
    $person->phone2 = $randomValue . '4';
    $person->phone3 = $randomValue . '5';
    return $person;
}
