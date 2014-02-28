<?php
namespace JordiLlonch\Component\Deployer\Deploy\Repository;

interface RepositoryInterface
{
    /**
     * @param $zone
     * @param $url
     */
    public function add($zone, $url);

    /**
     * @param $zone
     * @param int $format
     * @return string
     */
    public function listUrls($zone, $format = self::OUTPUT_YML);

    /**
     * @param $zone
     * @param $url
     */
    public function set($zone, $url);

    /**
     *
     */
    public function writeParameters();

    /**
     * @param $zone
     * @param $url
     */
    public function rm($zone, $url);

    /**
     * @param $path
     */
    public function readParameters($path);
}