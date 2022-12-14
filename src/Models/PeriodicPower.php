<?php
namespace Jasn\GeoHome\Models;

class PeriodicPower extends Base
{
    private $time;
    private $type;
    private $usage;
    
    public function __construct($power)
    {
        $this->usage = $power->totalConsumption;
        $this->time  = $power->readingTime;
        $this->type  = $power->commodityType;
    }
    public function getName()
    {
        if ($this->isGas()) {
            return 'Gas';
        } elseif ($this->isElectricity()) {
            return 'Electricity';
        }
    }
    public function getUsage()
    {
        return $this->usage;
    }
    public function getWattage()
    {
        return $this->watts;
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
