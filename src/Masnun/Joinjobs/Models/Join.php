<?php

namespace Masnun\Joinjobs\Models;

// Join, not JoinJob
class Join extends \Eloquent
{
    protected $table = "masnun_joins";
    public $timestamps = false;

    public function jobs()
    {
        return $this->hasMany('Masnun\Joinjobs\Models\Job', 'join_id');
    }
}