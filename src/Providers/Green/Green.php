<?php

namespace Sevming\Aliyun\Providers\Green;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\{ClientException, ServerException};
use Sevming\Aliyun\Providers\CloundClient;
use Sevming\Foundation\Exceptions\InvalidConfigException;

class Green extends CloundClient
{
    /**
     * Verify checksum.
     *
     * @param string|null $seed
     * @param string|null $content
     *
     * @return array|false
     */
    public function verifyChecksum(?string $seed = null, ?string $content = null)
    {
        $content = is_null($content) ? $this->app->request->getContent() : $content;
        parse_str($content, $data);
        $result = json_decode($data['content'], true);
        $checksum = hash('sha256', $this->app->config['uid'] . $seed . $data['content']);
        if ($checksum === $data['checksum']) {
            return $result;
        }

        return false;
    }

    /**
     * Image async scan.
     *
     * @param array       $tasks
     * @param array       $scenes
     * @param string|null $callbackUrl
     * @param string|null $seed
     * @param string|null $bizType
     * @param bool        $offline
     *
     * @return mixed|void
     * @throws ClientException|ServerException|InvalidConfigException
     */
    public function greenImageAsyncScan(
        array $tasks,
        array $scenes,
        ?string $callbackUrl = null,
        ?string $seed = null,
        ?string $bizType = null,
        bool $offline = false
    ) {
        $data = self::filterNullData([
            'tasks' => $tasks,
            'scenes' => $scenes,
            'callback' => $callbackUrl,
            'seed' => $seed,
            'bizType' => $bizType,
            'offline' => $offline,
        ]);
        $result = AlibabaCloud::roa()
            ->product('Green')
            ->version('2018-05-09')
            ->pathPattern('/green/image/asyncscan')
            ->method('POST')
            ->body(\json_encode($data))
            ->request();

        return $this->resolveData($result);
    }

    /**
     * Video async scan.
     *
     * @param array       $tasks
     * @param array       $scenes
     * @param string|null $callbackUrl
     * @param string|null $seed
     * @param bool        $live
     * @param string|null $audioScenes
     * @param string|null $bizType
     * @param bool        $offline
     *
     * @return mixed|void
     * @throws ClientException|ServerException|InvalidConfigException
     */
    public function greenVideoAsyncScan(
        array $tasks,
        array $scenes,
        ?string $callbackUrl = null,
        ?string $seed = null,
        bool $live = false,
        ?string $audioScenes = null,
        ?string $bizType = null,
        bool $offline = false
    ) {
        $data = self::filterNullData([
            'tasks' => $tasks,
            'scenes' => $scenes,
            'callback' => $callbackUrl,
            'seed' => $seed,
            'live' => $live,
            'bizType' => $bizType,
            'audioScenes' => $audioScenes,
            'offline' => $offline,
        ]);
        $result = AlibabaCloud::roa()
            ->product('Green')
            ->version('2018-05-09')
            ->pathPattern('/green/video/asyncscan')
            ->method('POST')
            ->body(\json_encode($data))
            ->request();

        return $this->resolveData($result);
    }
}
