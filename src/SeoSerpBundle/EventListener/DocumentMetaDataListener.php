<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 15/01/2020
 * Time: 14:55
 */

namespace SaltId\SeoSerpBundle\EventListener;

use Pimcore\Bundle\CoreBundle\EventListener\Traits\PimcoreContextAwareTrait;
use Pimcore\Http\Request\Resolver\{
    DocumentResolver as DocumentResolverService,
    PimcoreContextResolver
};
use Pimcore\Model\Document;
use SaltId\SeoSerpBundle\Helper\GeneralHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Pimcore\Templating\Helper\{HeadMeta, HeadTitle};
use SaltId\SeoSerpBundle\Model\{Seo, SeoRule};

class DocumentMetaDataListener implements EventSubscriberInterface
{
    use PimcoreContextAwareTrait;

    const FORCE_INJECTION = '_pimcore_force_document_meta_data_injection';

    /**
     * @var DocumentResolverService $documentResolverService
     */
    protected $documentResolverService;

    /**
     * @var HeadMeta $headMeta
     */
    protected $headMeta;

    /** @var HeadTitle $headTitle */
    protected $headTitle;

    /**
     * @param DocumentResolverService $documentResolverService
     * @param HeadMeta $headMeta
     * @param HeadTitle $headTitle
     */
    public function __construct(DocumentResolverService $documentResolverService, HeadMeta $headMeta, HeadTitle $headTitle)
    {
        $this->documentResolverService = $documentResolverService;
        $this->headMeta = $headMeta;
        $this->headTitle = $headTitle;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
        ];
    }

    /**
     * Finds the nearest document for the current request if the routing/document router didn't (e.g. static routes)
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        // just add meta data on master request
        if (!$event->isMasterRequest() && !$event->getRequest()->attributes->get(self::FORCE_INJECTION)) {
            return;
        }

        if (!$this->matchesPimcoreContext($request, PimcoreContextResolver::CONTEXT_DEFAULT)) {
            return;
        }

        $document = $this->documentResolverService->getDocument($request) ?? Document::getById(1);

        // do something magically to inject head meta tagging.
        $routeName = $request->get('_route');
        $getSeoRule = SeoRule::getByRouteName($routeName);

        if ($document && $getSeoRule && ($getSeoRule ? $getSeoRule->getActive() : false)) {
            $getRouteVariable = $getSeoRule->getRouteVariable();
            $getClassName = 'Pimcore\\Model\\DataObject\\' . $getSeoRule->getClassName();
            $getClassField = 'getBy' . ucfirst($getSeoRule->getClassField());
            $routeVariable = $request->get($getRouteVariable);

            $obj = $getClassName::$getClassField($routeVariable);

            $countObj = $obj->getCount();

            if ($countObj < 1) {
                return;
            }

            $object = $obj->getObjects()[0];
            $objectId = $object->getId();

            $seoRuleDefaultMetaData = $getSeoRule->getMetadata();

            if ($getSeoRule->getTitle()) {

                $document->setTitle(null);

                $defaultTitleGetter = 'get' . ucfirst($getSeoRule->getTitle());
                if (method_exists($object, $defaultTitleGetter)) {
                    $this->headTitle->set($object->$defaultTitleGetter());
                }
            }

            $decodeDefaultMetaData = json_decode($seoRuleDefaultMetaData, true);

            $seo = Seo::getByObjectId($objectId);
            $isShouldSkipSeo = !(bool)$seo;
            $seoData = $seo ? $seo->getData() : '';
            $decodeSeoData = json_decode($seoData, true);
            $seoMetaData = !$isShouldSkipSeo ? $this->extractSeo($decodeSeoData) : [];
            $seoMetaDataKeyValue = $this->extractSeoKeyValue($seoMetaData);

            if ($seoMetaDataKeyValue) {
                $seoRuleMetaDataKeyValue = $this->extractSeoKeyValue($decodeDefaultMetaData);
                $decodeDefaultMetaData = $this->cleansingDuplicateKeyValue(
                    $seoRuleMetaDataKeyValue,
                    $seoMetaDataKeyValue,
                    $decodeDefaultMetaData
                );
            }

            if ($decodeDefaultMetaData) {
                foreach ($decodeDefaultMetaData as $decodeDefaultMetaDatum) {
                    $defaultContent = $decodeDefaultMetaDatum['content'];
                    $defaultKeyValue = $decodeDefaultMetaDatum['keyValue'];
                    $defaultKeyType = $decodeDefaultMetaDatum['keyType'];
                    $trimString = $decodeDefaultMetaDatum['trim'];

                    $contentAsGetter = 'get' . ucfirst($defaultContent);
                    if (method_exists($object, $contentAsGetter)) {
                        $defaultContent = $object->$contentAsGetter();
                        $defaultContent = strip_tags($defaultContent);
                    }

                    if ($trimString) {
                        $defaultContent = GeneralHelper::trimText($defaultContent, $trimString, false);
                    }

                    $this->headMeta->__invoke($defaultContent, $defaultKeyValue, $defaultKeyType, []);
                }
            }

            if ($isShouldSkipSeo) {
                return;
            }

            if ($seoTitle = $decodeSeoData['seoTitle']) {
                $this->headTitle->set($seoTitle);
            }

            if ($seoDesc = $decodeSeoData['seoDescription']) {
                $this->headMeta->setDescription($seoDesc);
            }

            $metadata = $decodeSeoData['metadata'] ?? [];

            if (!$metadata) {
                return;
            }
            foreach ($metadata as $metadatum) {
                $content = $metadatum['content'];
                $keyValue = $metadatum['keyValue'];
                $keyType = $metadatum['keyType'];

                $this->headMeta->__invoke($content, $keyValue, $keyType, []);
            }
        }
    }

    private function extractSeo(array $decodeSeoData)
    {
        return $decodeSeoData['metadata'] ?? [];
    }

    private function extractSeoKeyValue(array $seoMetaData): array
    {
        $keyValue = [];

        if (!$seoMetaData) {
            return $keyValue;
        }

        foreach ($seoMetaData as $seoMetaDatum) {
            $keyValue[] = $seoMetaDatum['keyValue'] ?? null;
        }

        return $keyValue;
    }

    private function cleansingDuplicateKeyValue(array $seoRuleMetaDataKeyValue, array $seoMetaDataKeyValue, $decodeDefaultMetaData): array
    {
        $newDcodeDefaultMetaData = [];
        foreach ($seoRuleMetaDataKeyValue as $key => $item) {
            if (in_array($item, $seoMetaDataKeyValue, true)) {
                continue;
            }
            $newDcodeDefaultMetaData[] = $decodeDefaultMetaData[$key];
        }

        return $newDcodeDefaultMetaData;
    }
}