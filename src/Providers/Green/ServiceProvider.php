<?php

namespace Sevming\Aliyun\Providers\Green;

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
        $pimple['green'] = function ($app) {
            return new Green($app);
        };
    }
}