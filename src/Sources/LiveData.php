<?php
namespace Jasn\GeoHome\Sources;

class LiveData
{
    private $geohome    = null;
    private $last_fetch = 0;
    private $live_data  = null;

    public function __construct($geohome)
    {
        $this->geohome = $geohome;
    }

    public function getLiveData($device = null)
    {
        return $this->checkLiveData($device);
    }
    // Helpers
    private function checkLiveData($device = null)
    {
        // No Live Data or TTL Expired
        if (!$this->live_data || ($this->last_fetch + $this->live_data->ttl) > time()) {
            if ($device = $this->geohome->validateDevice($device)) {
                $this->live_data = $this->geohome->api('system/smets2-live-data/'.$device);
                $this->last_fetch = time();
            }
        }
        return $this->live_data;
    }
}
