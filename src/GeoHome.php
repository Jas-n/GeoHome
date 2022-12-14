<?php namespace Jasn\GeoHome;

use GuzzleHttp\Client;
use Jasn\GeoHome\Models\LivePower;
use Jasn\GeoHome\Models\PeriodicPower;
use Jasn\GeoHome\Sources\LiveData;
use Jasn\GeoHome\Sources\PeriodicData;

class GeoHome
{
    private $access_token  = null;
    private $api           = null;
    private $base_path     = '/api/userapi/';
    private $devices       = null;
    private $live_data     = null;
    private $password      = null;
    private $periodic_data = null;
    private $username      = null;

    public function __construct($username, $password)
    {
        $this->api = new Client([
            'base_uri' => 'https://api.geotogether.com/',
            'timeout'  => 10,
        ]);
        $this->password = $password;
        $this->username = $username;
    }
    public function api($uri)
    {
        $response = $this->api->get(
            $this->base_path.$uri,
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->access_token,
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ],
            ]
        );
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getBody()->getContents());
        }
    }
    public function getCurrentUsage($source_name = null)
    {
        if ($source_name !== null) {
            if (!in_array($source_name, ['Electricity', 'Gas'])) {
                return false;
            }
        }
        $sources = [];
        if ($data = $this->getLiveData()) {
            foreach ($data->power as $power) {
                $source = new LivePower($power);
                $sources[$source->getName()] = $source;
            }
            if ($source_name != null) {
                return $sources[$source_name];
            }
            return $sources;
        }
    }
    public function getDevices()
    {
        if ($this->init_api()) {
            return $this->devices;
        }
    }
    public function getDisplayName()
    {
        if ($this->init_api()) {
            return $this->display_name;
        }
    }
    public function getEmail()
    {
        if ($this->init_api()) {
            return $this->email;
        }
    }
    public function getLiveData()
    {
        if ($this->init_api()) {
            if (!$this->live_data) {
                $this->live_data = new LiveData($this);
            }
            return $this->live_data->getLiveData();
        }
    }
    public function getMeterReadings($source_name = null)
    {
        if ($source_name !== null) {
            if (!in_array($source_name, ['Electricity', 'Gas'])) {
                return false;
            }
        }
        $sources = [];
        if ($data = $this->getPeriodicData()) {
            foreach ($data->totalConsumptionList as $power) {
                $source = new PeriodicPower($power);
                $sources[$source->getName()] = $source;
            }
            if ($source_name != null) {
                return $sources[$source_name];
            }
            return $sources;
        }
    }
    public function getPeriodicData($device = null)
    {
        if ($this->init_api()) {
            if (!$this->periodic_data) {
                $this->periodic_data = new PeriodicData($this);
            }
        }
        return $this->periodic_data->getPeriodicData();
    }
    public function validateDevice($device = null)
    {
        if ($device === null) {
            $device = key($this->devices);
        }
        if ($key = array_search($device, $this->devices)) {
            $device = $this->devices[$key];
        } elseif (array_key_exists($device, $this->devices)) {
            $device = $this->devices[$device];
        } else {
            return false;
        }
        return $device;
    }
    // Helpers
    private function getAccessToken()
    {
        $response = $this->api->post(
            'usersservice/v2/login',
            [
                'body' => json_encode([
                    'identity' => $this->username,
                    'password' => $this->password,
                ]),
                'headers' => [
                    "Accept: application/json",
                    "Content-Type: application/json",
                ],
            ],
        );
        if ($response->getStatusCode() === 200) {
            $data = json_decode($response->getBody()->getContents());
            $this->access_token = $data->accessToken;
            $this->display_name = $data->displayName;
            $this->email        = $data->email;
            return true;
        }
    }
    private function getDeviceData()
    {
        if ($data = $this->api('v2/user/detail-systems?systemDetails=true')) {
            foreach ($data->systemDetails as $system) {
                $this->devices[$system->name] = $system->systemId;
            }
            return true;
        }
    }
    private function init_api()
    {
        if ($this->access_token
            || ($this->getAccessToken() && $this->getDeviceData())
        ) {
            return true;
        }
    }
}
