<?php
namespace Jasn\GeoHome\Sources;

class PeriodicData
{
    private $geohome       = null;
    private $last_fetch    = 0;
    private $periodic_data = null;

    public function __construct($geohome)
    {
        $this->geohome = $geohome;
    }

    public function getPeriodicData($device = null)
    {
        return $this->checkPeriodicData($device);
    }
    // Helpers
    private function checkPeriodicData($device = null)
    {
        // No Live Data or TTL Expired
        if (!$this->periodic_data || ($this->last_fetch + $this->periodic_data->ttl) > time()) {
            if ($device = $this->geohome->validateDevice($device)) {
                $this->periodic_data = $this->geohome->api('system/smets2-periodic-data/'.$device);
                $this->last_fetch    = time();
            }
        }
        return $this->periodic_data;
    }
}
