<?php

namespace Masnun\Joinjobs\Models;

class Job extends \Eloquent
{
    protected $table = "masnun_joinjobs_jobs";
    public $timestamps = false;

    public function joinjob()
    {
        return $this->belongsTo(
            'Masnun\Joinjobs\Models\Joinjob', 'superqueue_id'
        );
    }
}