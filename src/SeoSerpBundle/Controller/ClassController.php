<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 24/01/2020
 * Time: 11:48
 */

namespace SaltId\SeoSerpBundle\Controller;

use Pimcore\Model\DataObject\ClassDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/class")
 */
class ClassController extends AbstractController
{
    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @Route("/list")
     */
    public function listAction()
    {
        $res = [];
        $classes = new ClassDefinition\Listing();
        
        if ($classes->load()) {
            foreach ($classes->load() as $item) {
                $res[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }
        }

        return $this->json($res, 200);
    }

    /**
     * @param Request fie$request
     *
     * @Route("/fields")
     */
    public function fieldsAction(Request $request)
    {
        $res = [];
        $class = ClassDefinition::getByName($request->get('id'));

        if (!$class) {
            return $this->json($res, 200);
        }

        if (count($class->getFieldDefinitions()) < 1) {
            return $this->json($res, 200);
        }

        foreach ($class->getFieldDefinitions() as $fieldDefinition) {
            $res[] = [
                'name' => $fieldDefinition->getName(),
            ];
        }

        return $this->json($res, 200);
    }
}