<?php

define("CONFIG", require './config.php');

require './vendor/autoload.php';

(new \App\CrawlerYueHaiData())->run();