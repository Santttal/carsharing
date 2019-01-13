<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 01.07.18
 * Time: 19:34
 */

namespace App\Models;

use Illuminate\Support\Facades\Storage;

class SyncLog
{
    const FILE_NAME = 'sync.log';

    const LEVEL_SUCCESS = 'success';
    const LEVEL_FAILED = 'failed';

    const LIMIT = 10;

    /**
     * @var string
     */
    public $level;
    /**
     * @var string
     */
    public $message;

    public static function add(SyncLog $log)
    {
        $logs = static::load();
        array_unshift($logs, $log);
        static::save($logs);

    }

    /**
     * @param SyncLog[] $logs
     */
    public static function save(array $logs)
    {
        $data = [];
        foreach ($logs as $item) {
            $data[] = $item->toArray();
        }
        $data = array_slice($data, 0, self::LIMIT);

        Storage::disk('local')->put(static::FILE_NAME, json_encode($data));
    }

    /**
     * @return SyncLog[]
     */
    public static function load(): array
    {
        $logs = [];
        $data = json_decode(Storage::disk('local')->get(static::FILE_NAME), true);
        foreach ($data as $item) {
            $log = new SyncLog();
            $log->level = $item['level'];
            $log->message = $item['message'];
            $logs[] = $log;
        }

        return $logs;
    }

    public function toArray(): array
    {
        return [
            'level' => $this->level,
            'message' => $this->message,
        ];
    }
}
