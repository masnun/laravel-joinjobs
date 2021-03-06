<?php

namespace Masnun\Joinjobs;

use Masnun\Joinjobs\Models\Join;
use Masnun\Joinjobs\Models\Job;
use Jeremeamia\SuperClosure\SerializableClosure;

class JoinJobsManager
{
    public function getCurrentJoins()
    {
        return Join::where('is_complete', '=', false)->get();
    }

    // Clasess & Closures
    public function createJoin($handler = null, $autoDelete = false)
    {
        if (is_object($handler) && $handler instanceof \Closure)
        {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        }
        else
        {
            $handler = (string)$handler;
        }

        $join = new Join();
        $join->join_handler = $handler;
        $join->fully_dispatched = false;
        $join->auto_delete = $autoDelete;
        $join->created_at = new \DateTime();
        $join->save();

        return $join->id;
    }

    // Accept additional closure
    public function addJob($joinId, $handler = null, $isLastJob = false)
    {

        if (is_object($handler) && $handler instanceof \Closure)
        {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        }
        else
        {
            $handler = (string)$handler;
        }

        $job = new Job();
        $job->created_at = new \DateTime();
        $job->join_id = $joinId;
        $job->on_complete = $handler;
        $job->save();

        if ($isLastJob)
        {
            $this->setFullyDispatched($joinId);
        }

        $this->increaseJobsDispatchedCount($joinId);
        return $job->id;
    }


    public function addHandlerToJob($jobId, $handler)
    {
        if (is_object($handler) && $handler instanceof \Closure)
        {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        }
        else
        {
            $handler = (string)$handler;
        }

        $job = Job::findOrFail($jobId);
        $job->on_complete = $handler;
        return $job->save();
    }

    public function addHandlerToJoin($joinId, $handler)
    {
        if (is_object($handler) && $handler instanceof \Closure)
        {
            $handler = new SerializableClosure($handler);
            $handler = serialize($handler);
        }
        else
        {
            $handler = (string)$handler;
        }

        $join = Join::findOrFail($joinId);
        $join->join_handler = $handler;
        return $join->save();
    }


    public function setFullyDispatched($joinId)
    {
        $join = Join::findOrFail($joinId);
        $join->fully_dispatched = true;
        $result = $join->save();
        $this->processJoin($joinId);
        return $result;
    }

    public function completeJob($jobId)
    {
        $job = Job::findOrFail($jobId);
        $job->completed_at = new \DateTime();
        $job->is_complete = true;
        $job->save();

        $handler = $job->on_complete;

        if (!empty($handler))
        {
            if ($jobHandler = @unserialize($handler))
            {
                call_user_func($jobHandler);
            }
            else
            {
                $jobHandler = new $handler();
                $jobHandler->run();
            }
        }

        $this->increaseJobsCompletedCount($job->join_id);
        $this->processJoin($job->join_id);
    }

    public function processJoin($joinId)
    {
        $join = Join::findOrFail($joinId);
        $incompleteJobsCount = Job::where('join_id', '=', $joinId)
            ->where('is_complete', '=', false)
            ->count();

        if ($incompleteJobsCount < 1 && $join->fully_dispatched)
        {

            $join->is_complete = 1;
            $join->completed_at = new \DateTime();
            $join->save();

            $handler = $join->join_handler;
            if (!empty($handler))
            {
                if ($joinHandler = @unserialize($handler))
                {
                    call_user_func($joinHandler);
                }
                else
                {
                    $joinHandler = new $handler();
                    $joinHandler->run();
                }
            }

            if ($join->auto_delete)
            {
                Job::where('join_id', '=', $joinId)->delete();
                $join->delete();
            }


        }


    }

    public function getJobsStats($joinId)
    {
        $join = Join::findOrFail($joinId);
        return [
          'dispatched' => $join->jobs_dispatched,
          'completed' => $join->jobs_completed
        ];
    }

    public function deleteJoin($joinId)
    {
        $join = Join::findOrFail($joinId);
        Job::where('join_id', '=', $joinId)->delete();
        $join->delete();
    }

    protected function increaseJobsDispatchedCount($joinId)
    {
        $join = Join::findOrFail($joinId);
        $join->jobs_dispatched = (int)$join->jobs_dispatched + 1;
        return $join->save();
    }

    protected function increaseJobsCompletedCount($joinId)
    {
        $join = Join::findOrFail($joinId);
        $join->jobs_completed = (int)$join->jobs_completed + 1;
        return $join->save();
    }

}