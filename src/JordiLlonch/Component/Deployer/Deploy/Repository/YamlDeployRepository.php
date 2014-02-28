<?php


namespace JordiLlonch\Component\Deployer\Deploy\Repository;


use JordiLlonch\Component\Deployer\Deploy\Entity\Deploy;
use Symfony\Component\Yaml\Yaml;

class YamlDeployRepository implements DeployRepositoryInterface
{
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param Deploy $deploy
     */
    public function save(Deploy $deploy)
    {
        $data = array(
            'current_version' => $deploy->getCurrentVersion(),
            'last_downloaded_version' => $deploy->getLastDownloadedVersion(),
        );
        $yamlData = Yaml::dump($data);
        file_put_contents($this->path, $yamlData);
    }

    /**
     * @return Deploy
     */
    public function load()
    {
        $deploy = new Deploy();
        if (file_exists($this->path)) {
            $data = Yaml::parse($this->path);
            if (isset($data['current_version'])) $deploy->setCurrentVersion($data['current_version']);
            if (isset($data['last_downloaded_version'])) $deploy->setLastDownloadedVersion($data['last_downloaded_version']);
        }

        return $deploy;
    }
} 