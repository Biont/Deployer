<?php

namespace JordiLlonch\Component\Deployer\Deploy;

Class Container implements \Iterator
{
    /**
     * Array of Deploys
     * @var array
     */
    protected $deploys = array();

    protected $keys = array();
    protected $positionKey = 0;

    public function add(DeployInterface $deploy)
    {
        $this->keys[] = $deploy->getName();
        $this->deploys[$deploy->getName()] = $deploy;
    }

    public function get($name)
    {
        if (!isset($this->deploys[$name])) {
            throw new \Exception(sprintf('Deploy %s not found', $name));
        }

        return $this->deploys[$name];
    }

    public function getNames()
    {
        return $this->keys;
    }

    public function remove($name)
    {
        $this->keys = array_diff($this->key(), array($name));
        unset($this->deploys[$name]);
    }

    public function current()
    {
        return $this->deploys[$this->key()];
    }

    public function next()
    {
        ++$this->positionKey;
    }

    public function key()
    {
        return $this->keys[$this->positionKey];
    }

    public function valid()
    {
        return isset($this->keys[$this->positionKey]);
    }

    public function rewind()
    {
        $this->positionKey = 0;
    }
}
