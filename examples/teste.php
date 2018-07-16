<?php

require '../vendor/autoload.php';

//pr(server()->getElements());
//
//pr(request()->data());

Phacil\HTTP\Session::start();

pr(session()->getElements());
pr($_SESSION);
