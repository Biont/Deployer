<?php


namespace JordiLlonch\Component\Deployer\Deploy\Entity;


class Deploy
{
    /**
     * @var string
     */
    private $currentVersion;

    /**
     * @var string
     */
    private $lastDownloadedVersion;

    /**
     * @param string $currentVersion
     */
    public function setCurrentVersion($currentVersion)
    {
        $this->currentVersion = $currentVersion;
    }

    /**
     * @return string
     */
    public function getCurrentVersion()
    {
        return $this->currentVersion;
    }

    /**
     * @param string $lastDownloadedVersion
     */
    public function setLastDownloadedVersion($lastDownloadedVersion)
    {
        $this->lastDownloadedVersion = $lastDownloadedVersion;
    }

    /**
     * @return string
     */
    public function getLastDownloadedVersion()
    {
        return $this->lastDownloadedVersion;
    }
} 