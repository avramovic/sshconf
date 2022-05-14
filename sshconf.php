#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use Commands\AddHostCommand;
use Commands\EditHostCommand;
use Commands\ListHostsCommand;
use Commands\RemoveHostCommand;
use Commands\ViewHostCommand;
use Commands\ConnectHostCommand;
use Symfony\Component\Console\Application;

$application = new Application("sshconf", "0.4");

$application->add(new AddHostCommand());
$application->add(new EditHostCommand());
$application->add(new ViewHostCommand());
$application->add(new ListHostsCommand());
$application->add(new RemoveHostCommand());
$application->add(new ConnectHostCommand());

$application->setDefaultCommand('connect');

$application->run();