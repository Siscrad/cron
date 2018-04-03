<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 11:18
 */

namespace Cron\Job;

/**
 * Class Shell
 * @package Cron\Job
 */
class Shell extends Job
{
    
    /**
     * @var string
     */
    private $command;
    
    /**
     * @var array
     */
    private $arguments = [];
    
    
    /**
     * @param string $command
     *
     * @return Shell
     */
    public function setCommand(string $command): self
    {
        $this->command = $command;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }
    
    /**
     * @param string $argument
     *
     * @return Shell
     */
    public function addArgument(string $argument): self
    {
        if (!$argument) {
            return $this;
        }
        
        $this->arguments[] = $argument;
        
        return $this;
    }
    
    /**
     * @param array $arguments
     *
     * @return Shell
     */
    public function addArguments(array $arguments): self
    {
        if (!$arguments) {
            return $this;
        }
        
        foreach ($arguments as $argument) {
            $this->addArgument($argument);
        }
        
        return $this;
    }
    
    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }
    
    /**
     * @return Job
     */
    public function run(): Job
    {
        $cronOutput = null;
        $Scheduleresult = null;
        $arguments = implode(' ', $this->getArguments());
        $command = $this->getCommand() . ' ' . $arguments;
        
        exec($command, $cronOutput, $Scheduleresult);
        
        $this->setCronOutput($cronOutput);
        
        return $this;
    }
    
}