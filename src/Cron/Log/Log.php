<?php
/**
 * Desc
 *
 * @author Heiko Griga <h.griga@teilehaber.de>
 * @date   28.03.2018, 12:02
 */

namespace Cron\Log;

/**
 * Class Log
 * @package Cron\Log
 */
class Log
{
    
    public const STATUS_RUNNING = 0;
    public const STATUS_FINISHED = 1;
    public const STATUS_ERROR = 2;
    
    private const HTML_LINE_BREAK = '<br />';
    
    /**
     * @var string $file
     */
    private $file;
    
    /**
     * @var int $startTime
     */
    private $startTime = 0;
    
    /**
     * @var int $endTime
     */
    private $endTime = 0;
    
    /**
     * @var \mysqli $connection
     */
    private $connection;
    
    /**
     * @var int $insertId
     */
    private $insertId;
    
    /**
     * @var string $comment
     */
    private $comment = '';
    
    /**
     * @var int $status
     */
    private $status = 0;
    
    /**
     * Log constructor.
     *
     * @param string  $file
     * @param Database $connection
     */
    public function  __construct(string $file, Database $connection)
    {
        $this->file = $file;
        $this->connection = $connection;
    }

    public function start(): void
    {
        $this->startTime = date('Y-m-d H:i:s');
        $this->addStartToDatabase();
    }
    
    public function stop($status = self::STATUS_FINISHED): void
    {
        $this->status = $status;
        $this->endTime = date('Y-m-d H:i:s');
        $this->stopToDatabase();
    }
    
    /**
     * @param string $comment
     */
    public function addComment(string $comment): void
    {
        $comment = '[' . date('Y-m-d H:i:s') . '] ' . $comment . self::HTML_LINE_BREAK;
        $this->comment .= $this->connection->real_escape_string($comment);
        
        $sqlUpdate = 'UPDATE log
            SET comment = "' . $this->comment . '"
            WHERE id_log = ' . (int) $this->insertId;
        
        $this->connection->query($sqlUpdate);
    }
    
    private function addStartToDatabase(): void
    {
        
        $script = str_replace('/var/www/project/cronjobs/', '', $this->file);
        
        $idCron = $this->getCronJobIdFromFileName($script);
        
        $sqlInsert = 'INSERT INTO log (start_time, id_cronjob)
            VALUES ("'
            . $this->connection->real_escape_string($this->startTime) . '", '
            . (int) $idCron
            . ')';
        
        
        $this->connection->query($sqlInsert);
        $this->insertId = $this->connection->insert_id;
    }
    
    private function getCronJobIdFromFileName($fileName)
    {
        $sqlSelect = 'SELECT id_cronjob
            FROM cron
            WHERE file_name = "' . $this->connection->real_escape_string($fileName) . '"';
        
        return $this->connection->query($sqlSelect)->fetch_assoc()['id_cronjob'];
    }
    
    private function stopToDatabase(): void
    {
        $sqlUpdate = 'UPDATE log
            SET end_time = "'
            . $this->connection->real_escape_string($this->endTime)
            . '",
                status = ' . (int) $this->status . '
            WHERE id_log = ' . (int) $this->insertId;
        
        
        $this->connection->query($sqlUpdate);
        $this->insertId = $this->connection->insert_id;
    }
}