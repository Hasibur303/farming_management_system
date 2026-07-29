<?php
require_once __DIR__ . '/environment.php';

define('KINDWISE_API_KEY', env('KINDWISE_API_KEY', ''));
define(
    'KINDWISE_ENDPOINT',
    env('KINDWISE_ENDPOINT', 'https://crop.kindwise.com/api/v1/identification')
);
