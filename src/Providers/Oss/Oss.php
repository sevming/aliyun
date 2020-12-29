<?php

namespace Sevming\Aliyun\Providers\Oss;

use \Exception;
use OSS\Core\OssException;
use OSS\OssClient;
use Sevming\Foundation\Exceptions\InvalidConfigException;
use Sevming\Aliyun\Providers\BaseClient;

class Oss extends BaseClient
{
    /**
     * @var OssClient
     */
    protected $client;

    /**
     * @return OssClient
     * @throws OssException
     */
    public function getClient()
    {
        if (!($this->client instanceof OssClient)) {
            $this->client = new OssClient(
                $this->app->config['access_key_id'],
                $this->app->config['access_key_secret'],
                $this->app->config['oss']['endpoint'],
                $this->app->config['oss']['is_cname'] ?? false,
                $this->app->config['oss']['security_token'] ?? null,
                $this->app->config['oss']['request_proxy'] ?? null
            );
        }

        return $this->client;
    }

    /**
     * Generate data process string.
     *
     * @link https://help.aliyun.com/document_detail/44688.html?spm=a2c4g.11186623.6.1398.bed935a8nq0yeR
     *
     * @param array  $actions
     * @param string $process
     *
     * @return string
     */
    public static function generateDataProcessString(array $actions, string $process = 'image')
    {
        $prefix = "x-oss-process={$process}/";
        $options = [];
        foreach ($actions as $action => $params) {
            $temp = [$action];
            if (is_array($params)) {
                foreach ($params as $key => $value) {
                    $temp[] = "{$key}_{$value}";
                }
            } else {
                $temp[] = $params;
            }

            $options[] = implode(',', $temp);
        }

        return empty($actions) ? '' : ($prefix . implode($options, '/'));
    }

    /**
     * Upload file.
     *
     * @param string      $file
     * @param string|null $bucket
     * @param string|null $object
     * @param string|null $uploadDir
     * @param array       $options
     *
     * @return string
     * @throws OssException
     */
    public function uploadFile(string $file, ?string $bucket = null, ?string $object = null, ?string $uploadDir = null, ?array $options = null)
    {
        $bucket = is_null($bucket) ? $this->app->config['oss']['bucket'] : $bucket;
        if (empty($object)) {
            $object = (string)$uploadDir . date('Ymd') . '/' . basename(str_replace('\\', '/', $file));
        }

        $this->getClient()->uploadFile($bucket, $object, $file, $options);

        return $object;
    }

    /**
     * Delete file.
     *
     * @param string      $object
     * @param string|null $bucket
     *
     * @throws OssException
     */
    public function deleteFile(string $object, ?string $bucket = null)
    {
        if (!empty($object)) {
            $bucket = is_null($bucket) ? $this->app->config['oss']['bucket'] : $bucket;
            $this->getClient()->deleteObject($bucket, $object);
        }
    }

    /**
     * Generate policy.
     *
     * @link https://help.aliyun.com/document_detail/31988.html?spm=a2c4g.11186623.6.1674.7148438eSQvfMA
     *
     * @param array $config
     *
     * @return array
     * @throws InvalidConfigException|Exception
     */
    public function generatePolicy(array $config = [])
    {
        $uploadDir = !empty($config['dir']) ? $config['dir'] : ($this->app->config['oss']['policy']['dir'] ?? '');
        if (empty($uploadDir)) {
            throw new InvalidConfigException('INVALID CONFIG：Upload dir or size cannot be null.');
        }

        $uploadSize = !empty($config['size']) ? $config['size'] : ($this->app->config['oss']['policy']['size'] ?? 0);
        if (!(ctype_digit((string)$uploadSize) && $uploadSize > 0)) {
            throw new InvalidConfigException('INVALID CONFIG：Upload size must be an integer greater than zero.');
        }

        $uploadSize = (int)($uploadSize * 1024 * 1024);
        $effectiveTime = $this->app->config['oss']['policy']['effective_time'] ?? 60;
        $dateTime = (new \DateTime())->setTimezone(new \DateTimeZone($this->app->config['timezone'] ?? 'Asia/Shanghai'));
        $expirationTime = strtotime($dateTime->format('Y-m-d H:i:s')) + $effectiveTime;
        $base64Policy = base64_encode(\json_encode([
            'expiration' => self::getUtcExpirationTime($effectiveTime),
            'conditions' => [
                ['bucket' => $this->app->config['oss']['bucket']],
                ['content-length-range', 0, $uploadSize],
                ['starts-with', '$key', $uploadDir],
            ]
        ]));

        return [
            'access_key_id' => $this->app->config['access_key_id'],
            'host' => $this->app->config['oss']['host'],
            'policy' => $base64Policy,
            'signature' => base64_encode(hash_hmac('sha1', $base64Policy, $this->app->config['access_key_secret'], true)),
            'expiration_time' => $expirationTime,
            'upload_dir' => $uploadDir,
            'upload_size' => $uploadSize,
        ];
    }

    /**
     * @param int $effectiveTime
     *
     * @return string
     * @throws Exception
     */
    protected static function getUtcExpirationTime(int $effectiveTime)
    {
        $iso8601Date = new \DateTime();
        $iso8601DateStr = $iso8601Date->setTimezone(new \DateTimeZone('UTC'))
            ->add(new \DateInterval("PT{$effectiveTime}S"))
            ->format(\DateTime::ISO8601);
        $pos = strpos($iso8601DateStr, '+');

        return substr($iso8601DateStr, 0, $pos) . 'Z';
    }
}
