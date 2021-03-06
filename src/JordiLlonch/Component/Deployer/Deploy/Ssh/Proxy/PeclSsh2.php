<?php

/**
 * This file is part of the JordiLlonchDeployBundle
 *
 * (c) Jordi Llonch <llonch.jordi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This class is based in Idephix - Automation and Deploy tool
 * https://github.com/ideatosrl/Idephix
 *
 */

namespace JordiLlonch\Component\Deployer\Deploy\Ssh\Proxy;

class PeclSsh2 extends BaseProxy
{

    public function connect($host, $port)
    {
        if (!empty($this->connection)) {
            return $this->connection;
        }
        $this->connection = ssh2_connect($host, $port, null, array('disconnect', array($this, 'disconnect')));

        return $this->connection;
    }

    public function authByPassword($user, $password)
    {
        return ssh2_auth_password($this->connection, $user, $password);
    }

    public function authByPublicKey($user, $publicKeyFile, $privateKeyFile, $pwd)
    {
        return ssh2_auth_pubkey_file($this->connection, $user, $publicKeyFile, $privateKeyFile, $pwd);
    }

    public function authByAgent($user)
    {
        if (!function_exists('ssh2_auth_agent')) {
            throw new \Exception("ssh2_auth_agent does not exists");
        }

        return ssh2_auth_agent($this->connection, $user);
    }

    public function execute($cmd)
    {
        $stdout = ssh2_exec($this->connection, $cmd . '; echo "__RETURNS__:$?"', 'ansi');
        $stderr = ssh2_fetch_stream($stdout, SSH2_STREAM_STDERR);

        stream_set_blocking($stderr, true);
        $this->lastError = stream_get_contents($stderr);

        stream_set_blocking($stdout, true);
        $this->lastOutput = stream_get_contents($stdout);

        $returnCode = null;

        $pos = strpos($this->lastOutput, '__RETURNS__:');
        if (false !== $pos) {
            $returnCode = substr($this->lastOutput, $pos + 12);
            $this->lastOutput = substr($this->lastOutput, 0, $pos);
        }
        $this->lastExitCode = $returnCode;

        return $returnCode;
    }

    public function disconnect()
    {
        if (null !== $this->connection) {
            $this->execute('exit');
            $this->connection = null;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }
}
