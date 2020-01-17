<?php

namespace SaltId\SeoSerpBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class SeoSerpBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    public function getNiceName()
    {
        return 'SEO/SERP Bundle';
    }

    public function getDescription()
    {
        return 'SEO/SERP ! Improve your website ranking.';
    }

    public function getJsPaths()
    {
        return [
            '/bundles/seoserp/js/pimcore/seoRuleItem.js',
            '/bundles/seoserp/js/pimcore/seoRulePanel.js',
            '/bundles/seoserp/js/pimcore/seoTab.js',
            '/bundles/seoserp/js/pimcore/startup.js'
        ];
    }

    public function getCssPaths()
    {
        return [
            '/bundles/seoserp/css/seoserp.css'
        ];
    }

    protected function getComposerPackageName(): string
    {
        return 'saltid/pimcore-seoserpbundle';
    }

    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }
}