<?php
require_once __DIR__ . '/environment.php';

define('INSECT_API_KEY', env('INSECT_API_KEY', ''));
define(
    'INSECT_ENDPOINT',
    env('INSECT_ENDPOINT', 'https://insect.kindwise.com/api/v1/identification')
);
