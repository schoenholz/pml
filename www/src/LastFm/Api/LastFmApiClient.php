<?php

namespace App\LastFm\Api;

use GuzzleHttp\Client;

class LastFmApiClient
{
    const METHOD_USER_GET_WEEKLY_TRACK_CHART = 'user.getweeklytrackchart';
    const METHOD_USER_GET_WEEKLY_CHART_LIST = 'user.getweeklychartlist';

    /**
     * @var string
     */
    private $apiBaseUri;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var int
     */
    private $lastRequestTime = 0;

    /**
     * @var int
     */
    private $throttleTime;

    public function __construct()
    {
        // todo Move to config
        $this->apiBaseUri = 'http://ws.audioscrobbler.com/2.0/';
        $this->apiKey = 'eab63f573859ac80ac29c46b7c59c0a4';
        $this->throttleTime = 7;
    }

    public function get(array $args): array
    {
        $this->throttle();

        $args['api_key'] = $this->apiKey;
        $args['format'] = 'json';

        $res = (string) $this
            ->getHttpClient()
            ->get('', [
                'query' => $args,
            ])
            ->getBody()
        ;

        $this->lastRequestTime = time();

        $arr = json_decode($res, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('json_decode error: ' . json_last_error_msg());
        }

        return $arr;
    }

    private function throttle()
    {
        $now = time();

        if (
            $this->lastRequestTime
            && $this->lastRequestTime + $this->throttleTime > $now
        ) {
            sleep(($this->lastRequestTime + $this->throttleTime) - $now);
        }
    }

    private function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'base_uri' => $this->apiBaseUri,
            ]);
        }

        return $this->httpClient;
    }
}