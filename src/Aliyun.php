<?php

namespace Sevming\Aliyun;

use Sevming\Foundation\Foundation;

/**
 * Class Aliyun
 *
 * @property \Sevming\Aliyun\Providers\Green\Green $green
 */
class Aliyun extends Foundation
{
    /**
     * @var array
     */
    protected $providers = [
        Providers\Green\ServiceProvider::class
    ];
}