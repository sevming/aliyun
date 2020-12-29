<?php

namespace Sevming\Aliyun\Providers;

use Psr\Http\Message\ResponseInterface;
use Sevming\Aliyun\Aliyun;
use Sevming\Foundation\Supports\Response;
use Sevming\Foundation\Exceptions\InvalidConfigException;

class BaseClient
{
    /**
     * @var Aliyun
     */
    protected $app;

    /**
     * Constructor.
     *
     * @param Aliyun $app
     */
    public function __construct(Aliyun $app)
    {
        $this->app = $app;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function resolveData(ResponseInterface $response)
    {
        return Response::resolveData($response, $this->app->config['response_type']);
    }
}