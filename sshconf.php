#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Commands\AddHostCommand;
use Commands\EditHostCommand;
use Commands\ListHostsCommand;
use Commands\RemoveHostCommand;
use Symfony\Component\Console\Application;

$application = new Application("sshconf", "0.1");

$application->add(new AddHostCommand());
$application->add(new EditHostCommand());
$application->add(new ListHostsCommand());
$application->add(new RemoveHostCommand());

$application->run();