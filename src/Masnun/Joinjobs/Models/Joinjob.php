<?php

namespace Masnun\Joinjobs\Models;

class Joinjob extends \Eloquent
{
    protected $table = "masnun_joinjobs";
    public $timestamps = false;

    public function jobs()
    {
        return $this->hasMany('Masnun\Joinjobs\Models\Job', 'joinjob_id');
    }
}