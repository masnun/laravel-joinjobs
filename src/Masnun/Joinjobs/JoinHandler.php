<?php

/**
 * Author: Abu Ashraf Masnun
 * URL: http://masnun.me
 */

// This class is a sample of JoinHandler


namespace Masnun\Joinjobs;

class JoinHandler
{
    public function join()
    {
        echo "All done!" . PHP_EOL;
        mail("masnun@gmail.com", "test job ok", "done!");
    }
}