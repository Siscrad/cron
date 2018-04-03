<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 11:58
 */

namespace Cron;

use Cron\Interfaces\ScheduleInterface;
use Cron\Job\Job;

/**
 * Class Scheduler
 */
class Scheduler
{
    
    /**
     * @var Job[]
     */
    protected $jobs = [];
    
    /**
     * @var array
     */
    protected $output = [];
    
    /**
     * Create new jobs list
     *
     * @param array $jobs
     */
    public function addJobs(array $jobs): void
    {
        foreach ($jobs as $job) {
            $this->addJob($job);
        }
    }
    
    /**
     * Adds a new job to the list
     *
     * @param ScheduleInterface $job
     *
     * @return Scheduler $this
     */
    public function addJob(ScheduleInterface $job): self
    {
        $this->jobs[] = $job;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getJobs(): array
    {
        return $this->jobs;
    }
    
    /**
     * @return array
     */
    public function getOutput(): array
    {
        return $this->output;
    }
    
    /**
     * Runs any due job, returning an array containing the output from each job
     *
     * @return Scheduler
     */
    public function run(): self
    {
        $this->output = [];
        
        foreach ($this->jobs as $job) {
            if ($job->shouldRun()) {
                echo "can run \n";
                $result = $job->run()->getCronOutput();
                $this->output[] = [
                    'job'   => \get_class($job),
                    'output' => $job->getCronOutput(),
                    'result' => $result,
                ];
            }
        }
        
        return $this;
    }
}