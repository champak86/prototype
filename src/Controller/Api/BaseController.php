<?php

namespace App\Controller\Api;

use App\Constant\APIConstants;
use App\Constant\UserFields;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use JMS\Serializer\Serializer;
use App\Entity\User;
use Gedmo\Uploadable\FileInfo\FileInfoArray;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Stof\DoctrineExtensionsBundle\Uploadable;
use Gedmo\Uploadable\UploadableListener;

class BaseController extends AbstractFOSRestController
{
    public function validateUser($token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['accessToken' => $token]);
        if (!$user) {
            return null;
        }
        return $user;
    }

    public function getProductId($size)
    {
        $alpha_key = '';
        $keys = range('A', 'Z');
        for ($i = 0; $i < 2; $i++) {
            $alpha_key .= $keys[array_rand($keys)];
        }
        $length = $size - 2;
        $key = '';
        $keys = range(0, 9);
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $alpha_key . $key;
    }

    protected function failure($code = 404, $message)
    {
        $output = [];
        $output['error'] = array('code' => $code, 'description' => $message);
        $output['status'] = false;
        $output['response'] = null;
        $view = $this->view($output);

        return $this->handleView($view);
    }

    public function success($response, $context=["Default"], $code=200, $message=null)
    {
        $output = [];
        $output['error'] = null;
        $output['status'] = true;
        $output['message'] = $message;
        $output['data'] = $response;
        $view = $this->view($output, $code);
        $view = $view->setContext($view->getContext()->setGroups($context));
        return $this->handleView($view);
    }

}
