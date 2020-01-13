<?php

namespace CliModule\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        echo __ROOT__;
        die();
        echo 'cli ok';
    }
}