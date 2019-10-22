<?php

set_time_limit(0); 
date_default_timezone_set('PRC');

include './config.php';

$class_path = 'class';
include './'.$class_path.'/ContainerClass.php';
include './'.$class_path.'/TaskClass.php';
include './'.$class_path.'/vender/simplehtmldom_1_9/simple_html_dom.php';

$app = new ContainerClass($ENV);

