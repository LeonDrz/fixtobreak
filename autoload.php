<?php

function autoload($class)
{
    include "source/" . $class . ".php";
}

spl_autoload_register("autoload");
