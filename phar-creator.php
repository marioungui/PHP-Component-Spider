<?php
$archiveName = 'spider.phar';
$phar = new Phar($archiveName);
$phar->buildFromDirectory(__DIR__);
$phar->setStub("#!/usr/bin/env php\n\n" . $phar->createDefaultStub("spider.php"));