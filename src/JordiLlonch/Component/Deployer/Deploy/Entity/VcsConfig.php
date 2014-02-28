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

class VcsConfig
{
    private $url;
    private $branch;
    private $proxyRepositoryPath;

    /**
     * @param mixed $checkoutBranch
     */
    public function setBranch($checkoutBranch)
    {
        $this->branch = $checkoutBranch;
    }

    /**
     * @return mixed
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $proxyPath
     */
    public function setProxyRepositoryPath($proxyPath)
    {
        $this->proxyRepositoryPath = $proxyPath;
    }

    /**
     * @return mixed
     */
    public function getProxyRepositoryPath()
    {
        return $this->proxyRepositoryPath;
    }
}
