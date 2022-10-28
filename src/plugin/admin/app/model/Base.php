<?php

namespace plugin\admin\app\model;

use DateTimeInterface;
use support\Model;


class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

    /**
     * Format Date
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
