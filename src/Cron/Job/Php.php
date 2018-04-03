<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 11:18
 */

namespace Cron\Job;

/**
 * Class Php
 * @package Cron\Job
 */
class Php extends Job
{
    
    /**
     * @var string
     */
    private $script;
    
    /**
     * @var array
     */
    private $arguments = [];
    
    
    /**
     * @param string $script
     *
     * @return Php
     */
    public function setScript(string $script): self
    {
        $this->script = $script;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getScript(): string
    {
        return $this->script;
    }
    
    /**
     * @param string $argument
     *
     * @return Php
     */
    public function addArgument(string $argument): self
    {
        if (!$argument) {
            return $this;
        }
        
        $this->arguments[] = escapeshellarg($argument);
        
        return $this;
    }
    
    /**
     * @param array $arguments
     *
     * @return Php
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
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }
    
    /**
     * @param string $rootPath
     *
     * @return Php
     */
    public function setRootPath(string $rootPath): self
    {
        $this->rootPath = $rootPath;
        
        return $this;
    }
    
    /**
     * @return Job
     */
    public function run(): Job
    {
        $cronOutput = null;
        $Scheduleresult = null;
        $arguments = implode(' ', $this->getArguments());
        $command = 'php "' . $this->rootPath . '/' . $this->getScript() . '" ' . $arguments;
        
        
        exec(escapeshellcmd($command), $cronOutput, $Scheduleresult);
        
        $this->setCronOutput($cronOutput);
        
        return $this;
    }
    
    public function __construct()
    {
        $this->rootPath = str_replace('\\', '/', \dirname(__DIR__, 3)) . '/cronjobs';
    }
    
}