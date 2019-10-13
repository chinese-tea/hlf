<?php

include './load.php';

$configArr = $ENV_USER['15240651556'];

$task = new TaskClass($app, $configArr);

$task->run();

