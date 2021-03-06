<?php
/**
 * Copyright (c) 2018 Wolf Utz <wpu@hotmail.de>
 *
 * This file is part of the OmegaBlog project.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Service;

use App\Exception\ConfigurationNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ConfigurationService.
 */
class ConfigurationService
{
    /**
     * @var null|ContainerInterface
     */
    private $container = null;

    /**
     * ConfigurationService constructor.
     *
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return Yaml::parseFile($this->getConfigurationFilePath());
    }

    /**
     * @param string $name
     *
     * @return mixed
     *
     * @throws ConfigurationNotFoundException
     */
    public function getConfigurationEntry(string $name)
    {
        $configuration = $this->getConfiguration();
        if (!array_key_exists($name, $configuration)) {
            throw new ConfigurationNotFoundException("Configuration entry with name $name not found!", 1519060809);
        }

        return $configuration[$name];
    }

    /**
     * @param array $newConfiguration
     */
    public function updateConfiguration(array $newConfiguration)
    {
        $configuration = $this->getConfiguration();
        foreach ($newConfiguration as $key => $value) {
            if (!key_exists($key, $configuration)) {
                continue;
            }
            $configuration[$key] = $value;
        }
        file_put_contents($this->getConfigurationFilePath(), Yaml::dump($configuration));
    }

    /**
     * @return string
     */
    private function getConfigurationFilePath(): string
    {
        return $this->container->get('kernel')->getProjectDir().'/config/blog.yaml';
    }

    /**
     * @throws \Exception
     */
    public function initializeConfigurationFile()
    {
        if(file_exists($this->getConfigurationFilePath())) {
            throw new \Exception(
                "The configuration file for the blog already exist. Call this method only while you fresh install the blog",
                1520350395
            );
        }

        copy($this->getConfigurationFilePath().'.dist', $this->getConfigurationFilePath());
    }
}
