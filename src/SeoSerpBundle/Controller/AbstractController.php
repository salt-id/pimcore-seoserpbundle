<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 15/01/2020
 * Time: 17:51
 */

namespace SaltId\SeoSerpBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Pimcore\Tool\Session;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractController extends FrontendController
{
    public function onKernelController(FilterControllerEvent $event)
    {
        parent::onKernelController($event);

        $session = Session::getReadOnly();
        $user = $session->get('user');
        if (!$user instanceof \Pimcore\Model\User) {
            throw new HttpException(401, 'NO NO NO AUTH ');
        }
    }
}