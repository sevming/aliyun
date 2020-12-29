<?php

namespace Sevming\Aliyun\Providers\Mts;

use Sevming\Foundation\Exceptions\InvalidConfigException;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Request\RpcRequest;
use AlibabaCloud\Client\Exception\{ClientException, ServerException};
use Sevming\Aliyun\Providers\CloundClient;

class Mts extends CloundClient
{
    /**
     * @return RpcRequest
     * @throws ClientException
     */
    protected function initializeMts()
    {
        return AlibabaCloud::rpc()
            ->product('Mts')
            ->scheme('https')
            ->version('2014-06-18')
            ->method('POST')
            ->host($this->app->config['mts']['host']);
    }

    /**
     * Submit snapshot job.
     *
     * @param string      $inputObject
     * @param string|null $outputObject
     * @param int         $time
     * @param int|null    $num
     * @param int|null    $interval
     *
     * @return mixed|void
     * @throws ClientException|ServerException|InvalidConfigException
     */
    public function submitSnapshotJob(string $inputObject, ?string $outputObject = null, int $time = 1, ?int $num = null, ?int $interval = null)
    {
        $snapshotConfig = [];
        if (!is_null($num) || !is_null($interval)) {
            if (!isset($this->app->config['mts']['pipeline_id'])) {
                throw new InvalidConfigException('INVALID ARGUMENT: PipelineId cannot be null.');
            }

            if ($num && $interval) {
                $snapshotConfig['Num'] = $num;
                $snapshotConfig['Interval'] = $interval;
            }
        }

        if (empty($outputObject)) {
            $outputObject = substr($inputObject, 0, strrpos($inputObject, '.')) . (!empty($snapshotConfig) ? '_{Count}' : '') . '.jpg';
        }

        $data = self::filterNullData([
            'Input' => \json_encode([
                'Bucket' => $this->app->config['mts']['bucket'],
                'Location' => $this->app->config['mts']['location'],
                'Object' => $inputObject,
            ]),
            'PipelineId' => $this->app->config['mts']['pipeline_id'] ?? null,
            'SnapshotConfig' => \json_encode(array_merge([
                'OutputFile' => [
                    'Bucket' => $this->app->config['mts']['bucket'],
                    'Location' => $this->app->config['mts']['location'],
                    'Object' => urlencode($outputObject),
                ],
                'Time' => $time,
            ], $snapshotConfig))
        ]);
        $result = $this->initializeMts()
            ->action('SubmitSnapshotJob')
            ->options(['query' => $data])
            ->request();

        return $this->resolveData($result);
    }

    /**
     * Query snapshot job.
     *
     * @param string|array $jobList
     *
     * @return mixed|void
     * @throws ClientException|ServerException|InvalidConfigException
     */
    public function querySnapshotJobList($jobList)
    {
        if (!is_array($jobList)) {
            $jobList = [$jobList];
        }

        $data = self::filterNullData([
            'PipelineId' => $this->app->config['mts']['pipeline_id'] ?? null,
            'SnapshotJobIds' => implode(',', $jobList),
        ]);
        $result = $this->initializeMts()
            ->action('QuerySnapshotJobList')
            ->options(['query' => $data])
            ->request();

        return $this->resolveData($result);
    }
}
