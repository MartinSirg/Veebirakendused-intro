<?php

require_once('common.php');

if ($argc < 2) {
    print 'Pass directory to scan as an argument' . PHP_EOL;
    exit(-1);
} else {
    $path = realpath($argv[1]);
}

if ($path === false) {
    error('Argument is not a correct directory');
}

$it = new RecursiveDirectoryIterator($path);
$it = new RecursiveIteratorIterator($it);
$it = new RegexIterator($it, '/\.(\w+)$/i', RecursiveRegexIterator::GET_MATCH);

$counts = getCounts($it);

if (!isset($counts['css'])
    || !isset($counts['html'])
    || $counts['css'] < 1
    || $counts['html'] < 2) {

    print 'Repository must contain at least two files with html '
        . 'extension and one file with css extension'
        . PHP_EOL;

    printf(RESULT_PATTERN, 0);

    exit(-1);
} else {
    printf(RESULT_PATTERN, 10);
}


function getCounts($it) {
    $counts = [];

    foreach($it as $each) {
        $ext = strtolower($each[1]);

        $counts[$ext] = isset($counts[$ext])
            ? $counts[$ext] + 1
            : 1;
    }

    return $counts;
}
