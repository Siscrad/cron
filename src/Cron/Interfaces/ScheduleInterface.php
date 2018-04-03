<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 10:41
 */

namespace Cron\Interfaces;

use Cron\Job\Job;

/**
 * Interface ScheduleInterface
 * @package Cron\Interfaces
 */
interface ScheduleInterface
{
   
    public function run(): Job;
    
    public function shouldRun(): bool;
    
    public function getCronOutput(): array;
}