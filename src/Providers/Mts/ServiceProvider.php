<?php

namespace Sevming\Aliyun\Providers\Mts;

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
        $pimple['mts'] = function ($app) {
            return new Mts($app);
        };
    }
}