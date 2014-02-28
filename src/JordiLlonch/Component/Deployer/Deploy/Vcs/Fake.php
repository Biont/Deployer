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

use Psr\Log\LoggerInterface;

class Fake implements VcsInterface
{
    public function __construct()
    {
    }

    public function setLogger(LoggerInterface $logger)
    {
    }

    public function initialize()
    {
    }

    public function setDestinationPath($destinationPath)
    {
    }

    protected function exec($command, &$output = null)
    {
        $output = array();

        return '';
    }

    public function cloneCodeRepository()
    {
    }

    public function getHashFromLastVersionCode()
    {
        return 'LAST_VERSION_HASH';
    }

    public function getHeadHash($pathVcs = null)
    {
        return 'HEAD_HASH';
    }

    public function pushLastDeployTag($tag, $pathVcs = null)
    {
    }

    public function getDiffFiles($dirFrom, $dirTo)
    {
        return array();
    }
}