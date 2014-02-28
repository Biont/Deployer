<?php


namespace JordiLlonch\Component\Deployer\Deploy\OperationSystem;


use JordiLlonch\Component\Deployer\Deploy\Entity\SshConfig;

class Commands
{
    public function mkdirRecursive($path)
    {
        return sprintf('mkdir -p "%s"', $path);
    }

    public function chmod($mode, $path)
    {
        return sprintf('chmod %s "%s"', $mode, $path);
    }

    public function ls($path)
    {
        return sprintf('ls "%s"', $path);
    }

    public function removeRecursive($path)
    {
        return sprintf('rm -rf "%s"', $path);
    }

    public function copyRecursive($mode, $path)
    {
        return sprintf('cp -a "%s"', $mode, $path);
    }

    public function rsync($pathOrigin, $pathTarget, $rsyncParameters = '')
    {
        return sprintf('rsync -ar --delete %s "%s" "$s"', $rsyncParameters, $pathOrigin, $pathTarget);
    }

    public function rsyncSsh($pathOrigin, $pathTarget, SshConfig $sshConfig, $rsyncParameters = '')
    {
        return sprintf('rsync -ar --delete -e "ssh -p %s -i \"%s\" -l %s -o \"UserKnownHostsFile=/dev/null\" -o \"StrictHostKeyChecking=no\"" %s "%s" "%s:%s"',
            $sshConfig->getPort(),
            $this->sanitizePath($sshConfig->getPrivateKeyFile()),
            $sshConfig->getUser(),
            $rsyncParameters,
            $pathOrigin,
            $sshConfig->getHost(),
            $pathTarget);
    }

    public function createSymbolicLink($sourcePath, $targetPath)
    {
        return sprintf('ln -sfn "%s" "%s"', $sourcePath, $targetPath);
    }

    public function sanitizePath($path)
    {
        $sanitizedPath = str_replace('~', $_SERVER['HOME'], $path);

        return $sanitizedPath;
    }
}