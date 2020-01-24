<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 23/01/2020
 * Time: 19:25
 */

namespace SaltId\SeoSerpBundle\Controller;

use Pimcore\Model\Staticroute;
use SaltId\SeoSerpBundle\Helper\GeneralHelper;
use SaltId\SeoSerpBundle\Model\SeoRule;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/route")
 */
class RouteController extends AbstractController
{
    /**
     * @Route("/list")
     */
    public function getNameAction()
    {
        $res = [];
        $routes = new Staticroute\Listing();

        $seoRule = new SeoRule\Listing();
        $existingSeoRule = array_filter($seoRule->toArray(['*'], ['routeName']));

        if ($routes->load()) {
            /** @var Staticroute $route */
            foreach ($routes->load() as $route) {
                if (in_array($route->getName(), $existingSeoRule)) {
                    continue;
                }
                $res[] = [
                    'id' => $route->getName(),
                    'name' => $route->getName()
                ];
            }
        }

        return $this->json($res, 200);
    }

    /**
     * @param Request $request
     * @Route("/variables")
     */
    public function getVariableAction(Request $request)
    {
        $res = [];
        $route = Staticroute::getByName($request->get('id'));

        $explodes = explode(',', GeneralHelper::removeSpace($route->getVariables()));
        if (!$explodes) {
            return $res;
        }

        foreach ($explodes as $explode) {
            $res[] = [
                'name' => $explode
            ];
        }

        return $this->json($res, 200);
    }
}