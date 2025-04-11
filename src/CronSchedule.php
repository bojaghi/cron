<?php

namespace Bojaghi\Cron;

use Bojaghi\Contract\Module;
use Bojaghi\Helper\Helper;

class CronSchedule implements Module
{
    private array $items;

    public function __construct(array|string $config)
    {
        $config = Helper::loadConfig($config);
        $config = wp_parse_args(
            $config,
            [
                'items' => [
                    // Sample item
                    // [
                    //     'display'  => '', // Human-readable, descriptive title.
                    //     'internal' => 0,  // Schedule interval,
                    //     'schedule' => '', // Schedule name. Only lowercase, number, and underscore are recommended.
                    // ]
                ],
            ],
        );

        $this->items = $config['items'];

        add_filter('cron_schedules', [$this, 'cronSchedules']);
    }

    public function cronSchedules(array $schedules): array
    {
        foreach ($this->items as $item) {
            $item = self::validateItem($item);
            if (!$item) {
                continue;
            }
            $schedules[$item['schedule']] = [
                'interval' => $item['interval'],
                'display'  => $item['display'],
            ];
        }

        return $schedules;
    }

    public static function validateItem(array $item): array|false
    {
        $item = wp_parse_args(
            $item,
            [
                'schedule' => '',
                'interval' => 0,
                'display'  => '',
            ],
        );

        return ($item['schedule'] && $item['interval'] > 0 && $item['display']) ? $item : false;
    }
}
