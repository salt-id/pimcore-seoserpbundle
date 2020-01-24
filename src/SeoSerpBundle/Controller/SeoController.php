<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 15/01/2020
 * Time: 17:52
 */

namespace SaltId\SeoSerpBundle\Controller;

use Pimcore\Db;
use Pimcore\Model\DataObject\ClassDefinition;
use SaltId\SeoSerpBundle\Helper\GeneralHelper;
use SaltId\SeoSerpBundle\Installer;
use SaltId\SeoSerpBundle\Model\Seo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seo")
 */
class SeoController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/get", methods={"GET"})
     */
    public function getSeo(Request $request)
    {
        $className = $request->get('className');
        $objectId = $request->get('objectId');

        /** @var ClassDefinition $getClassDefinition */
        $getClassDefinition = ClassDefinition::getByName($className);

        if (!$getClassDefinition) {
            $res = [
                'hasSeoAbleTrait' => false,
                'data' => null
            ];

            return $this->json($res, 200);
        }

        $hasSeoAbleTrait = in_array(
            '\SaltId\SeoSerpBundle\Traits\Seoable',
            explode(
                ',',
                GeneralHelper::removeSpace($getClassDefinition->getUseTraits())),
            false
        );
        $data = null;

        if ($hasSeoAbleTrait) {
            $getSeoSerpData = Seo::getByObjectId($objectId);

            $data = $getSeoSerpData ? json_decode($getSeoSerpData->getData(), true) : null;
        }

        $res = [
            'hasSeoAbleTrait' => $hasSeoAbleTrait,
            'data' => $data
        ];

        return $this->json($res, 200);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/put", methods={"PUT"})
     */
    public function putSeo(Request $request)
    {
        $objectId = $request->get('objectId');

        $checkObjectId = Seo::getByObjectId($objectId);

        $data = json_encode($request->request->all());

        if (!$checkObjectId) {
            $newSeoData = new Seo();
            $newSeoData->setObjectId($objectId);
            $newSeoData->setData($data);

            $newSeoData->save();
        }

        if ($checkObjectId) {
            $checkObjectId->setData($data);
            $checkObjectId->save();
        }

        return $this->json(['success' => true], 200);
    }
}