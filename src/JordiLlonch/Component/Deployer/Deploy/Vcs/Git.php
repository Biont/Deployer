<?php
/**
 * This file is part of the JordiLlonchDeployBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JordiLlonch\Component\Deployer\Deploy\Vcs;

use JordiLlonch\Component\Deployer\Deploy\Entity\VcsConfig;
use JordiLlonch\Component\Deployer\Deploy\Process\Process;
use JordiLlonch\Component\Deployer\Exception\VcsBranchNotFoundException;
use JordiLlonch\Component\Deployer\Exception\VcsException;
use JordiLlonch\Component\Deployer\Exception\VcsRepositoryEmptyException;
use Psr\Log\LoggerInterface;

class Git implements VcsInterface
{
    /**
     * @var VcsConfig
     */
    protected $config;

    /**
     * @var Process;
     */
    protected $process;

    protected $logger;

    public function __construct(VcsConfig $config, Process $process)
    {
        $this->config = $config;
        $this->process = $process;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function initialize()
    {
        if (!$this->existsProxyRepositoryPath()) {
            $branch = $this->config->getBranch();
            $url = $this->config->getUrl();
            $proxyRepositoryPath = $this->config->getProxyRepositoryPath();
            $this->cloneRepository($branch, $url, $proxyRepositoryPath);
        }
    }

    public function cloneCodeRepository($destinationPath)
    {
        $repositoryPath = $this->config->getProxyRepositoryPath();
        $branch = $this->config->getBranch();
        $this->updateProxyRepository($repositoryPath);
        $this->cloneProxyRepositoryToDestinationPath($repositoryPath, $branch, $destinationPath);
    }

    public function getHashFromLastVersionCode()
    {
        $repositoryPath = $this->config->getProxyRepositoryPath();
        $branch = $this->config->getBranch();

        $vcsVersions = $this->getReferencesListFromRemote();
        if (empty($vcsVersions[0])) throw new VcsRepositoryEmptyException(null, 0, null, $repositoryPath);

        $vcsVersion = '';
        foreach ($vcsVersions as $item) if (\preg_match('/' . $branch . '/', $item)) $vcsVersion = $item;
        if (empty($vcsVersion)) throw new VcsBranchNotFoundException(null, 0, null, $repositoryPath, $branch);

        $vcsVersion = explode("\t", $vcsVersion);
        $vcsVersion = $vcsVersion[0];
        if (empty($vcsVersion)) throw new VcsException('Unable to get last git version.', 0, null, $repositoryPath);

        return $vcsVersion;
    }

//    public function getHeadHash($pathVcs = null)
//    {
//        // Check if repositoryDir exists
//        if(!file_exists($pathVcs . '/.git')) return 'HEAD';
//
//        if(is_null($pathVcs)) $pathVcs = $this->destinationPath;
//        $hash = $this->process->execute('git --git-dir="' . $pathVcs . '/.git" rev-parse HEAD');
//
//        return $hash;
//    }
//
//    public function pushLastDeployTag($tag, $pathVcs = null)
//    {
//        // Add tag
//        if(is_null($pathVcs)) $pathVcs = $this->destinationPath;
//        $headHash = $this->getHeadHash();
//        $this->process->execute('git --git-dir="' . $pathVcs . '/.git" fetch --tags');
//        $this->process->execute('git --git-dir="' . $pathVcs . '/.git" tag -f "' . $tag . '" ' . $headHash);
//
//        // Delete tag
//        $this->process->execute('git --git-dir="' . $pathVcs . '/.git" push --tags origin :refs/tags/' . $tag);
//        // Push to origin
//        $this->process->execute('git --git-dir="' . $pathVcs . '/.git" push --tags origin ' . $this->branch);
//    }
//
//    public function getDiffFiles($dirFrom, $dirTo)
//    {
//        if (!$this->isProxy) throw new \Exception(__METHOD__ . ' method only works if zone uses a repository proxy.');
//
//        $gitUidFrom = $this->getHeadHash($dirFrom);
//        $gitUidTo = $this->getHeadHash($dirTo);
//        if ($gitUidFrom && $gitUidFrom) {
//            $urlParsed = parse_url($this->url);
//            $this->process->execute('git --git-dir="' . $urlParsed['path'] . '/.git" diff ' . $gitUidTo . ' ' . $gitUidFrom . ' --name-only', $diffFiles);
//
//            return $diffFiles;
//        }
//
//        return array();
//    }

    /**
     * @return bool
     */
    protected function existsProxyRepositoryPath()
    {
        return file_exists($this->config->getProxyRepositoryPath());
    }

    protected function updateProxyRepository($repositoryPath)
    {
        $this->process->execute(sprintf('git --git-dir="%s/.git" --work-tree="%s" reset --hard HEAD', $repositoryPath, $repositoryPath));
        $this->process->execute(sprintf('git --git-dir="%s/.git" --work-tree="%s" pull', $repositoryPath, $repositoryPath));
    }

    /**
     * @param $repositoryPath
     * @param $branch
     * @param $destinationPath
     */
    protected function cloneProxyRepositoryToDestinationPath($repositoryPath, $branch, $destinationPath)
    {
        $this->cloneRepositoryDepth1($branch, $repositoryPath, $destinationPath);
        $this->overwriteRemoteOrigin($repositoryPath, $destinationPath);
    }

    /**
     * @param $repositoryPath
     * @param $destinationPath
     */
    protected function overwriteRemoteOrigin($repositoryPath, $destinationPath)
    {
        $process = $this->process->execute(sprintf('git --git-dir="%s/.git" config --get remote.origin.url', $repositoryPath));
        $originUrlProxyRepo = $process->getOutput();
        $this->process->execute(sprintf('git --git-dir="%s/.git" config --replace-all remote.origin.url "%s"', $destinationPath, $originUrlProxyRepo));
    }

    /**
     * @param $branch
     * @param $url
     * @param $destinationPath
     */
    protected function cloneRepositoryDepth1($branch, $url, $destinationPath)
    {
        $this->process->execute(sprintf('git clone --branch "%s" "%s" "%s" --depth=1', $branch, $url, $destinationPath));
    }

    /**
     * @param $branch
     * @param $url
     * @param $destinationPath
     */
    protected function cloneRepository($branch, $url, $destinationPath)
    {
        $this->process->execute(sprintf('git clone --branch "%s" "%s" "%s"', $branch, $url, $destinationPath));
    }

    /**
     * @return array|string
     */
    protected function getReferencesListFromRemote()
    {
        $repositoryPath = $this->config->getProxyRepositoryPath();
        $branch = $this->config->getBranch();
        $process = $this->process->execute(sprintf('git --git-dir="%s/.git" ls-remote origin %s', $repositoryPath, $branch));
        $vcsVersions = $process->getOutput();
        $vcsVersions = explode("\n", $vcsVersions);

        return $vcsVersions;
    }
}