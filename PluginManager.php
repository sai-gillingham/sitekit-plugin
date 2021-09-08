<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SiteKit;


use Eccube\Plugin\AbstractPluginManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class PluginManager extends AbstractPluginManager
{
    public function install(array $meta, ContainerInterface $container)
    {
        $fs = new Filesystem();
        $routeYaml = $container->getParameter('plugin_data_realdir').'/SiteKit/routes.yaml';
        if (!$fs->exists($routeYaml)) {
            $fs->dumpFile($routeYaml, '');
        }
    }

    public function update(array $meta, ContainerInterface $container)
    {
        $fs = new Filesystem();
        $routeYaml = $container->getParameter('plugin_data_realdir').'/SiteKit/routes.yaml';

        // site認証ファイルがある場合はルーティング生成
        $verificationFile = $container->getParameter('plugin_data_realdir').'/SiteKit/google-site-verification.txt';
        if ($fs->exists($verificationFile)) {
            $token = file_get_contents($verificationFile);
            $token = ltrim($token, 'google-site-verification: ');
            $yaml = Yaml::dump([
                'site_kit_google_site_verification' => [
                    'path' => '/google'.$token.'.html',
                    'controller' => 'Plugin\SiteKit\Controller\Admin\ConfigController::siteVerification',
                ]
            ]);
            $fs->dumpFile($routeYaml, $yaml);
        } else {
            $fs->dumpFile($routeYaml, '');
        }
    }
}
