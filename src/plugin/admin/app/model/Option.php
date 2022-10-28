<?php

namespace plugin\admin\app\model;


/**
 * @property integer $id (primary key)
 * @property string $name 键
 * @property mixed $value 值
 * @property string $created_at creation time
 * @property string $updated_at Update time
 */
class Option extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_options';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

}
