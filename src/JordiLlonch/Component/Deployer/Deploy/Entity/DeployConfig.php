<?php

/**
 * This file is part of the JordiLlonchDeployBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JordiLlonch\Component\Deployer\Deploy\Entity;

class DeployConfig
{
    private $name;
    private $environment;

    private $helperConfig;
    private $customConfig;

    private $versionNew;
    private $versionCurrent;

    private $dryMode = false;
    private $numberOfDeploysToLeftOnClean = 7;

    private $sudoEnabled = false;

    private $localRootPath;
    private $remoteRootPath;

    /**
     * @param int $cleanMaxDeploys
     */
    public function setNumberOfDeploysToLeftOnClean($cleanMaxDeploys)
    {
        $this->numberOfDeploysToLeftOnClean = $cleanMaxDeploys;
    }

    /**
     * @return int
     */
    public function getNumberOfDeploysToLeftOnClean()
    {
        return $this->numberOfDeploysToLeftOnClean;
    }

    /**
     * @param array $customConfig
     */
    public function setCustomConfig(array $customConfig)
    {
        $this->customConfig = $customConfig;
    }

    /**
     * @return mixed
     */
    public function getCustomConfig()
    {
        return $this->customConfig;
    }

    public function enableDryMode()
    {
        $this->dryMode = true;
    }

    public function disableDryMode()
    {
        $this->dryMode = false;
    }

    /**
     * @return boolean
     */
    public function isDryModeEnabled()
    {
        return $this->dryMode;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @param mixed $helperConfig
     */
    public function setHelperConfig($helperConfig)
    {
        $this->helperConfig = $helperConfig;
    }

    /**
     * @return mixed
     */
    public function getHelperConfig()
    {
        return $this->helperConfig;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function enableSudo()
    {
        $this->sudoEnabled = true;
    }

    public function disableSudo()
    {
        $this->sudoEnabled = false;
    }

    /**
     * @return boolean
     */
    public function isSudoEnabled()
    {
        return $this->sudoEnabled;
    }

    /**
     * @param mixed $versionCurrent
     */
    public function setVersionCurrent($versionCurrent)
    {
        $this->versionCurrent = $versionCurrent;
    }

    /**
     * @return mixed
     */
    public function getVersionCurrent()
    {
        return $this->versionCurrent;
    }

    /**
     * @param mixed $versionNew
     */
    public function setVersionNew($versionNew)
    {
        $this->versionNew = $versionNew;
    }

    /**
     * @return mixed
     */
    public function getVersionNew()
    {
        return $this->versionNew;
    }

    /**
     * @param mixed $rootPath
     */
    public function setLocalRootPath($rootPath)
    {
        $this->localRootPath = $rootPath;
    }

    /**
     * @return mixed
     */
    public function getLocalRootPath()
    {
        return $this->localRootPath;
    }

    /**
     * @param mixed $remoteRootPath
     */
    public function setRemoteRootPath($remoteRootPath)
    {
        $this->remoteRootPath = $remoteRootPath;
    }

    /**
     * @return mixed
     */
    public function getRemoteRootPath()
    {
        return $this->remoteRootPath;
    }


    public function getLocalReleasesPath()
    {
        return $this->getLocalRootDeployPath() . DIRECTORY_SEPARATOR . 'releases';
    }

    public function getLocalDataPath()
    {
        return $this->getLocalRootDeployPath() . DIRECTORY_SEPARATOR . 'data';
    }

    public function getLocalRootDeployPath()
    {
        return $this->getLocalRootPath() . DIRECTORY_SEPARATOR . $this->getName();
    }

    public function getRemoteCurrentPath()
    {
        return $this->getRemoteRootPath() . DIRECTORY_SEPARATOR . 'current';
    }

    public function getRemoteReleasesPath()
    {
        return $this->getRemoteRootPath() . DIRECTORY_SEPARATOR . 'releases';
    }

    public function getRemoteSharedPath()
    {
        return $this->getRemoteRootPath() . DIRECTORY_SEPARATOR . 'shared';
    }

    public function getRemoteRootDeployPath()
    {
        return $this->getRemoteRootPath() . DIRECTORY_SEPARATOR . $this->getName();
    }
}
