<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 10:45
 */

namespace Cron\Job;

use \Cron\Interfaces\ScheduleInterface;
use \Cron\Expression\Expression;

/**
 * Class Job
 * @package Cron\Job
 */
abstract class Job implements ScheduleInterface
{
    
    /**
     * @var string
     */
    protected $rootPath;
    
    /**
     * @var string
     */
    protected $cronExpression = '';
    
    /**
     * @var array
     */
    protected $cronOutput = [];
    
    /**
     * @return string
     */
    public function getCronExpression(): string
    {
        return $this->cronExpression;
    }
    
    /**
     * @param string $cronExpression
     *
     * @return $this
     */
    public function setCronExpression(string $cronExpression): self
    {
        $this->cronExpression = $cronExpression;
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getCronOutput(): array
    {
        return $this->cronOutput;
    }
    
    /**
     * @param array $cronOutput
     *
     * @return $this
     */
    public function setCronOutput(array $cronOutput): self
    {
        $this->cronOutput = $cronOutput;
    
        return $this;
    }
    
    /**
     * @return bool
     */
    public function shouldRun(): bool
    {
        $cronExpression = $this->getCronExpression();
        
        if (empty($cronExpression)) {
            return false;
        }
        
        return Expression::isTimeCron(time(), $cronExpression);
    }
    
    abstract public function run(): self;
    
}