<?php
/**
 * @author Jordi Llonch <llonch.jordi@gmail.com>
 * @date 29/09/13 17:27
 */

namespace JordiLlonch\Component\Deployer\Deploy\CopyStrategy;


interface CopyStrategyInterface
{
    public function syncronize($localPath, $remotePath);
} 