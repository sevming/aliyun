<?php

namespace Sevming\Aliyun\Providers;

use Sevming\Aliyun\Aliyun;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;

class CloundClient extends BaseClient
{
    /**
     * Constructor.
     *
     * @param Aliyun $app
     *
     * @throws ClientException
     */
    public function __construct(Aliyun $app)
    {
        parent::__construct($app);
        $this->initialize();
    }

    /**
     * @throws ClientException
     */
    protected function initialize()
    {
        AlibabaCloud::accessKeyClient($this->app->config['access_key_id'], $this->app->config['access_key_secret'])
            ->regionId($this->app->config['region_id'])
            ->asDefaultClient();
        AlibabaCloud::setLogger($this->app->log);
        AlibabaCloud::setLogFormat($this->app->config->get('http.log_template', "Request >>>>>>>>\n{request}\n<<<<<<<< Response\n{response}\n--------\nError {error}\n"));
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function filterNullData(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}