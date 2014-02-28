<?php


namespace JordiLlonch\Component\Deployer\Deploy\Entity;


abstract class BaseEnum
{
    private $value;

    protected function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
