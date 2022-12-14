<?php
namespace Jasn\GeoHome\Models;

class Base
{
    public function getFormattedUsage($dp = 2)
    {
        $usage = $this->getUsage();
        $unit  = $this->getUnit($usage);
        if ($this->isElectricity() && $usage >= 1000) {
            $usage = $usage / 1000;
        }
        return round($usage, $dp).$unit;
    }
    public function getUnit($usage = null)
    {
        if ($this->isGas()) {
            return 'm3';
        } elseif ($this->isElectricity()) {
            if ($usage && $usage >= 1000) {
                return 'kw';
            }
            return 'w';
        }
    }
    public function getUsage()
    {
        return $this->usage;
    }
    public function isElectricity()
    {
        return $this->type === 'ELECTRICITY';
    }
    public function isGas()
    {
        return $this->type === 'GAS_ENERGY';
    }
}
