<?php

namespace Masnun\Joinjobs;

use Masnun\Joinjobs\Models\Joinjob;
use Masnun\Joinjobs\Models\Job;

class JoinJobsManager
{
    public function getCurrentJoinjobs()
    {
        return Joinjob::where('is_complete', '=', false)->get();
    }

    // Clasess & Closures
    public function createJoinJob($handler)
    {
        $joinjob = new Joinjob();
        $joinjob->join_handler = (string)$handler;
        $joinjob->save();

        return $joinjob->id;
    }

    // Accept additional closure
    public function addJob($joinjobId, $closure = null)
    {
        $job = new Job();
        $job->created_at = new \DateTime();
        $job->joinjob_id = $joinjobId;
        $job->save();

        return $job->id;
    }

    public function completeJob($jobId)
    {
        $job = Job::findOrFail($jobId);
        $job->completed_at = new \DateTime();
        $job->is_complete = true;
        $job->save();

        $this->processJoinJob($job->joinjob_id);
    }

    public function processJoinJob($joinjobId)
    {
        $joinjob = Joinjob::findOrFail($joinjobId);
        $incompleteJobsCount = Job::where('joinjob_id', '=', $joinjobId)
            ->where('is_complete', '=', false)
            ->count();

        if ($incompleteJobsCount < 1) {
            $handler = $joinjob->join_handler;
            $joinHandler = new $handler();
            $joinHandler->join();

            $joinjob->is_complete = 1;
            $joinjob->save();
        }


    }

}