<?php

namespace GaoweiSpace\Weather;

use GaoweiSpace\Weather\Exceptions\HttpException;
use GaoweiSpace\Weather\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];



    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $guzzleOptions)
    {
        $this->guzzleOptions = $guzzleOptions;
    }

    public function getWeather($city, $type = 'base', $format = 'json')
    {
        $url = 'https://restapi.amap.com/v3/weather/weatherInfo';

        if (!\in_array(\strtolower($format), ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }

        if (!\in_array(\strtolower($type), ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type value(base/all): ' . $type);
        }

        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => \strtolower($format),
            'extensions' => \strtolower($type),
        ]);

        try {
            $response = $this->getHttpClient()->get($url, [
                'query' => $query,
            ])->getBody()->getContents();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return 'json' === $format ? \json_decode($response, true) : $response;
    }
}