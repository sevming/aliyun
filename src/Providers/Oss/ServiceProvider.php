<?php

namespace Sevming\Aliyun\Providers\Oss;

use Pimple\{Container, ServiceProviderInterface};

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     *
     * @param Container $pimple
     */
    public function register(Container $pimple)
    {
        $pimple['oss'] = function ($app) {
            return new Oss($app);
        };
    }
}