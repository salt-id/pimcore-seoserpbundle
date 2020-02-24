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

        $document = $this->documentResolverService->getDocument($request);

        // do something magically to inject head meta tagging.
        $routeName = $request->get('_route');
        $getSeoRule = SeoRule::getByRouteName($routeName);

        if ($getSeoRule && $getSeoRule->getActive()) {
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

            $decodeDefaultMetaData = json_decode($seoRuleDefaultMetaData, true);

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

            $seo = Seo::getByObjectId($objectId);
            if (!$seo) {
                return;
            }

            $seoData = $seo->getData();
            if (!is_json($seoData)) {
                return;
            }

            $decodeSeoData = json_decode($seoData, true);
            $metadata = $decodeSeoData['metadata'] ?? null;

            if ($decodeSeoData['seoTitle']) {
                $this->headTitle->set($decodeSeoData['seoTitle']);
            }

            if ($decodeSeoData['seoDescription']) {
                $this->headMeta->setDescription($decodeSeoData['seoDescription']);
            }

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
}