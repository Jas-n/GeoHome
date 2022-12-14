<?php
namespace Jasn\GeoHome\Models;

class LivePower extends Base
{
    private $type;
    private $watts;
    private $usage;
    private $valueAvailable;
    
    public function __construct($power)
    {
        foreach ($power as $key => $value) {
            $this->$key = $value;
        }
        $this->usage = $this->watts;
    }
    public function getFormattedUsage()
    {
        $usage = $this->getUsage();
        print_r($usage);
        return number_format($usage).$this->getUnit($usage);
    }
    public function getName()
    {
        if ($this->isGas()) {
            return 'Gas';
        } elseif ($this->isElectricity()) {
            return 'Electricity';
        }
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
