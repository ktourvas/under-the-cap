<?php

require_once __DIR__ . '/../vendor/autoload.php';

use UnderTheCap\Participation;

$participation = new Participation();
echo $participation->sayHello();
