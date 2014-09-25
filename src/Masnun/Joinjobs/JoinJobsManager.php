<?php

namespace Masnun\Joinjobs;

use Masnun\Joinjobs\Models\Joinjob;
use Masnun\Joinjobs\Models\Job;
use Jeremeamia\SuperClosure\SerializableClosure;

class JoinJobsManager
{
    public function getCurrentJoinjobs()
    {
        return Joinjob::where('is_complete', '=', false)->get();
    }

    // Clasess & Closures
    public function createJoinJob($handler = null)
    {
        if (is_object($handler) && $handler instanceof \Closure) {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        } else {
            $handler = (string)$handler;
        }

        $joinjob = new Joinjob();
        $joinjob->join_handler = $handler;
        $joinjob->save();

        return $joinjob->id;
    }

    // Accept additional closure
    public function addJob($joinjobId, $handler = null)
    {

        if (is_object($handler) && $handler instanceof \Closure) {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        } else {
            $handler = (string)$handler;
        }

        $job = new Job();
        $job->created_at = new \DateTime();
        $job->joinjob_id = $joinjobId;
        $job->on_complete = $handler;
        $job->save();

        return $job->id;
    }


    public function addHandlerToJob($jobId, $handler)
    {
        if (is_object($handler) && $handler instanceof \Closure) {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        } else {
            $handler = (string)$handler;
        }

        $job = Job::findOrFail($jobId);
        $job->on_complete = $handler;
        return $job->save();
    }

    public function addHandlerToJoinJob($joinjobId, $handler)
    {
        if (is_object($handler) && $handler instanceof \Closure) {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        } else {
            $handler = (string)$handler;
        }

        $joinjob = Joinjob::findOrFail($joinjobId);
        $joinjob->join_handler = $handler;
        return $joinjob->save();
    }

    public function completeJob($jobId)
    {
        $job = Job::findOrFail($jobId);
        $job->completed_at = new \DateTime();
        $job->is_complete = true;
        $job->save();

        $handler = $job->on_complete;

        if (!empty($handler)) {
            if ($jobHandler = @unserialize($handler)) {
                call_user_func($jobHandler);
            } else {
                $jobHandler = new $handler();
                $jobHandler->join();
            }
        }


        $this->processJoinJob($job->joinjob_id);
    }

    public function processJoinJob($joinjobId)
    {
        $joinjob = Joinjob::findOrFail($joinjobId);
        $incompleteJobsCount = Job::where('joinjob_id', '=', $joinjobId)
            ->where('is_complete', '=', false)
            ->count();

        if ($incompleteJobsCount < 1) {

            $joinjob->is_complete = 1;
            $joinjob->save();

            $handler = $joinjob->join_handler;
            if (!empty($handler)) {
                if ($joinHandler = @unserialize($handler)) {
                    call_user_func($joinHandler);
                } else {
                    $joinHandler = new $handler();
                    $joinHandler->join();
                }
            }


        }


    }

}