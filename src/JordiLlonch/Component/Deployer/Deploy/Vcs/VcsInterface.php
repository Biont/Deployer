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

interface VcsInterface
{
    public function initialize();

    public function getHashFromLastVersionCode();

    public function cloneCodeRepository($destinationPath);

//    public function getDiffFiles($dirFrom, $dirTo);
//    public function pushLastDeployTag($tag, $pathVcs = null);
//    public function getHeadHash($pathVcs = null);
}