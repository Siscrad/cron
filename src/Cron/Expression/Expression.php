<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   27.03.2018, 15:48
 */

namespace Cron\Expression;

/**
 * Class Expression
 */
class Expression
{

    private const MINUTE = 0;
    private const HOUR = 1;
    private const DAY_OF_MONTH = 2;
    private const MONTH = 3;
    private const DAY_OF_WEEK = 4;
    
    /**
     * @var string $executionTime
     */
    private $executionTime;
    
    /**
     * @var string $expression
     */
    private $expression;
    
    /**
     * Expression constructor.
     *
     * @param string $expression
     */
    public function __construct(string $expression)
    {
        $this->expression = $expression;
        $this->executionTime = explode(' ', date('i H d m Y"'));
        
    }
    
    /**
     * @param $time
     * @param $expression
     *
     * @return bool
     */
    public static function isTimeCron($time , $expression): bool
    {
        $expressionParts = explode(' ', $expression);
        
        [$min , $hour , $day , $mon , $week] = explode(' ' , $expression);
        
        $toCheck = ['min' => 'i' , 'hour' => 'G' , 'day' => 'j' , 'mon' => 'n' , 'week' => 'w'];
        
        $ranges = [
            'min' => '0-59',
            'hour' => '0-23',
            'day' => '1-31',
            'mon' => '1-12',
            'week' => '0-6'
        ];
        
        foreach($toCheck as $toCheckKey => $format)
        {
            $expression = ${$toCheckKey};
            $expressionues = [];
            
            $values = array();
            
            if(strpos($expression , '/') !== false) {
                /**
                 * 0-23/2
                 */
                
                //Get the range and step
                [$range , $steps] = explode('/' , $expression);
                
                //Now get the start and stop
                if($range === '*')
                {
                    $range = $ranges[$toCheckKey];
                }
                list($start , $stop) = explode('-' , $range);
                
                for($i = $start ; $i <= $stop ; $i = $i + $steps)
                {
                    $expressionues[] = $i;
                }
            } else {
                /**
                 * 2 || 2,3,4 || 1-23
                 */
                $k = explode(',' , $expression);
                
                foreach($k as $v)
                {
                    if(strpos($v , '-') !== false)
                    {
                        [$start ,$stop] = explode('-' , $v);
                        
                        for($i = $start ; $i <= $stop ; $i++)
                        {
                            $expressionues[] = $i;
                        }
                    }
                    else
                    {
                        $expressionues[] = $v;
                    }
                }
            }
            
            if ((string)$expression !== '*' && !\in_array(date($format , $time), $expressionues)) {
                return false;
            }
        }
        
        return true;
    }
    
    
    private function isAllowedToRun(): bool
    {
        
        $expressionParts = explode(' ', $this->expression);
        
        foreach ($expressionParts as $type => $expressionPart) {
            switch ($type) {
                // minute
                case self::MINUTE:
                    echo PHP_EOL . ' minute ' . PHP_EOL;
                    if (!$this->validateMinute($expressionPart)) {
                        echo PHP_EOL . 'Minute: nope' . PHP_EOL;
                        return false;
                    }
                    echo PHP_EOL . 'Minute: yep' . PHP_EOL;
                    break;
                // hour
                case self::HOUR:
                    echo PHP_EOL . ' hour ' . PHP_EOL;
                    if (!$this->validateHour($expressionPart)) {
                        echo PHP_EOL . 'Hour: nope' . PHP_EOL;
                        return false;
                    }
                    echo PHP_EOL . 'Hour: yep' . PHP_EOL;
                    break;
                // day of month
                case self::DAY_OF_MONTH:
                    echo PHP_EOL . ' day ' . PHP_EOL;
                    if (!$this->validateDayOfMonth($expressionPart)) {
                        echo PHP_EOL . 'Day: nope' . PHP_EOL;
                        return false;
                    }
                    echo PHP_EOL . 'Day: yep' . PHP_EOL;
                    break;
                // month
                case self::MONTH:
                    echo PHP_EOL . ' month ' . PHP_EOL;
                    if (!$this->validateDayOfMonth($expressionPart)) {
                        echo PHP_EOL . 'Month: nope' . PHP_EOL;
                        return false;
                    }
                    echo PHP_EOL . 'Month: yep' . PHP_EOL;
                    break;
                // day of week
                case self::DAY_OF_WEEK:
                    echo PHP_EOL . ' day of week ' . PHP_EOL;
                    if (!$this->validateDayOfMonth($expressionPart)) {
                        echo PHP_EOL . 'Day of week: nope' . PHP_EOL;
                        return false;
                    }
                    echo PHP_EOL . 'Day of week: yep' . PHP_EOL;
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * @param string $expression
     *
     * @return bool
     */
    private function validateMinute(string $expression): bool
    {
        $slashPosition = strpos($expression, '/');
        $isCommaSeparated = strpos($expression, ',') !== false;
        
        if ($slashPosition !== false) {
            $divisor = (int) substr($expression, $slashPosition + 1);
            
            if (0 === $this->executionTime[self::MINUTE] % $divisor) {
                return true;
            }
        } elseif ('*' === $expression) {
            return true;
        } elseif ($isCommaSeparated) {
            $minutes = explode(',', $expression);
            
            return \in_array((int) $this->executionTime[self::MINUTE], $minutes);
        } else {
            $minute = (int) $expression;
            return (int) $this->executionTime[self::MINUTE] === $minute;
        }
        
        return false;
    }
    
    /**
     * @param string $expression
     *
     * @return bool
     */
    private function validateHour(string $expression): bool
    {
        $slashPosition = strpos($expression, '/');
        $isCommaSeparated = strpos($expression, ',') !== false;
        
        if ($slashPosition !== false) {
            $divisor = (int) substr($expression, $slashPosition + 1);
            
            if (0 === $this->executionTime[self::HOUR] % $divisor) {
                return true;
            }
        } elseif ('*' === $expression) {
            return true;
        } elseif ($isCommaSeparated) {
            $minutes = explode(',', $expression);
            
            return \in_array((int) $this->executionTime[self::HOUR], $minutes);
        } else {
            $minute = (int) $expression;
            return (int) $this->executionTime[self::HOUR] === $minute;
        }
        
        return false;
    }
    
    /**
     * @param string $expression
     *
     * @return bool
     */
    private function validateDayOfMonth(string $expression): bool
    {
        $slashPosition = strpos($expression, '/');
        $isCommaSeparated = strpos($expression, ',') !== false;
        
        if ($slashPosition !== false) {
            $divisor = (int) substr($expression, $slashPosition + 1);
            
            if (0 === $this->executionTime[self::DAY_OF_MONTH] % $divisor) {
                return true;
            }
        } elseif ('*' === $expression) {
            return true;
        } elseif ($isCommaSeparated) {
            $minutes = explode(',', $expression);
            
            return \in_array((int) $this->executionTime[self::DAY_OF_MONTH], $minutes);
        } else {
            $minute = (int) $expression;
            return (int) $this->executionTime[self::DAY_OF_MONTH] === $minute;
        }
        
        return false;
    }
    
    private function validateMonth(string $expression): bool
    {
        $slashPosition = strpos($expression, '/');
        $isCommaSeparated = strpos($expression, ',') !== false;
        
        if ($slashPosition !== false) {
            $divisor = (int) substr($expression, $slashPosition + 1);
            
            if (0 === $this->executionTime[self::MONTH] % $divisor) {
                return true;
            }
        } elseif ('*' === $expression) {
            return true;
        } elseif ($isCommaSeparated) {
            $month = explode(',', $expression);
            
            return \in_array((int) $this->executionTime[self::MONTH], $month);
        } elseif (is_numeric($expression)) {
            $month = (int) $expression;
            return (int) $this->executionTime[self::MONTH] === $month;
        } else {
            $month = (int) $expression;
            $monthMapping = [
                'jan' => 1,
                'feb' => 2,
                'mar' => 3,
                'apr' => 4,
                'may' => 5,
                'jun' => 6,
                'jul' => 7,
                'aug' => 8,
                'sep' => 9,
                'oct' => 10,
                'nov' => 11,
                'dec' => 12
            ];
            
            if (isset($monthMapping[$this->expression[self::MONTH]]) && $month ===
                $monthMapping[$this->expression[self::MONTH]]) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param string $expression
     *
     * @return bool
     */
    public static function shouldRun(string $expression): bool
    {
        return (new Expression($expression))->isAllowedToRun();
    }
}