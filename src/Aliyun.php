<?php

namespace Sevming\Aliyun;

use Sevming\Foundation\Foundation;

/**
 * Class Aliyun
 *
 * @property \Sevming\Aliyun\Providers\Green\Green $green
 * @property \Sevming\Aliyun\Providers\Mts\Mts     $mts
 * @property \Sevming\Aliyun\Providers\Oss\Oss     $oss
 */
class Aliyun extends Foundation
{
    /**
     * @var array
     */
    protected $providers = [
        Providers\Green\ServiceProvider::class,
        Providers\Mts\ServiceProvider::class,
        Providers\Oss\ServiceProvider::class
    ];
}