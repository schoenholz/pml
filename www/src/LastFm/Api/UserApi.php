<?php

namespace App\LastFm\Api;

class UserApi
{
    /**
     * @var LastFmApiClient
     */
    private $client;

    public function __construct(LastFmApiClient $client)
    {
        $this->client = $client;
    }

    public function getWeeklyChartList(string $user): array
    {
        return $this->client->get([
            'method' =>LastFmApiClient::METHOD_USER_GET_WEEKLY_CHART_LIST,
            'user' => $user,
        ]);
    }

    public function getWeeklyTrackChart(
        string $user,
        \DateTime $from = null,
        \DateTime $to = null
    ): array {
        $args = [
            'method' => LastFmApiClient::METHOD_USER_GET_WEEKLY_TRACK_CHART,
            'user' => $user,
        ];

        if ($from) {
            $args['from'] = $from->getTimestamp();
        }

        if ($to) {
            $args['to'] = $to->getTimestamp();
        }

        return $this->client->get($args);
    }
}