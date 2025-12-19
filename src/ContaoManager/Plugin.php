<?php

declare(strict_types=1);

/*
 * (c) INSPIRED MINDS
 */

namespace InspiredMinds\ContaoTurboHelper\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use InspiredMinds\ContaoTurboHelper\ContaoTurboHelperBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoTurboHelperBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
