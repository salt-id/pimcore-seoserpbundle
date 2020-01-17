<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 17/01/2020
 * Time: 10:58
 */

namespace SaltId\SeoSerpBundle\Controller;

use Pimcore\Model\Staticroute;
use SaltId\SeoSerpBundle\Model\SeoRule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seorule")
 */
class SeoRuleController extends AbstractController
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/get", methods={"GET"})
     */
    public function detailAction(Request $request)
    {
        $seoRule = SeoRule::getById($request->get('id'));

        $res = [];

        if ($seoRule) {
            $objvars = get_object_vars($seoRule);

            foreach ($objvars as $k => $objvar) {
                $getter = 'get' . ucfirst($k);

                $res[$k] = $seoRule->$getter();
            }
        }

        return $this->json($res, 200);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/add", methods={"POST"})
     */
    public function addAction(Request $request)
    {
        //need params = name;
        //return params = success => true, id => x;

        $seoRule = new SeoRule();
        $seoRule->setName($request->get('name'));
        $seoRule->save();

        return $this->json(['success' => true, 'id' => $seoRule->getId()]);
    }

    /**
     * @param Request $request
     *
     * @Route("/delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request)
    {
        $success = false;

        $seoRule = SeoRule::getById($request->get('id'));
        if ($seoRule) {
            $seoRule->delete();
            $success = true;
        }
        return $this->json(['success' => $success], 200);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/save", methods={"PUT"})
     */
    public function saveAction(Request $request)
    {
        $data = json_decode($request->get('data'), true);

        /** @var SeoRule $seoRule */
        $seoRule = SeoRule::getById($request->get('id'));
        $seoRule->setValues($data['settings']);
        $seoRule->save();

        return $this->json(['success' => true], 200);
    }

    /**
     * @param Request $request
     *
     * @Route("/list", methods={"GET"})
     */
    public function listAction(Request $request)
    {
        //return seoRule dao listing
        $seoRules = [];

        /** @var SeoRule\Listing $list */
        $list = new SeoRule\Listing();

        /** @var SeoRule $seoRule */
        foreach ($list->load() as $seoRule) {
            $seoRules[] = [
                'id' => $seoRule->getId(),
                'text' => $seoRule->getName(),
                'routeName' => $seoRule->getRouteName(),
                'routeVariable' => $seoRule->getRouteVariable(),
                'className' => $seoRule->getClassName(),
                'classField' => $seoRule->getClassField(),
                'active' => $seoRule->getActive()
            ];
        }

        return $this->json($seoRules, 200);
    }

    /**
     * @param Request $request
     *
     * @Route("/config", methods={"GET"})
     */
    public function getConfig(Request $request)
    {
        $staticRoute = $this->getStaticRoute();

        $res = [
            'routes' => $staticRoute
        ];

        return $this->json($res, 200);
    }

    private function getStaticRoute()
    {
        $routes = [];
        $staticRoute = new Staticroute\Listing();
        $staticRoute->load();

        if (!$staticRoute->getRoutes()) {
            return $routes;
        }

        foreach ($staticRoute->getRoutes() as $route) {
            $routes[] = [
                'id' => $route->getId(),
                'name' => $route->getName(),
                'variables' => $this->variableExploder($route->getVariables()),
            ];
        }

        return $routes;
    }

    /**
     * @param string $variables
     * @return array
     */
    private function variableExploder(string $variables)
    {
        $exploder = explode(
            ',',
            preg_replace('/\s+/', '', $variables)
        );

        return $exploder;
    }
}