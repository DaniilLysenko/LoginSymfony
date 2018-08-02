<?php

namespace App\Security\Authentication\Handler;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthSuccessHandler implements AuthenticationSuccessHandlerInterface, ContainerAwareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $em = $this->container->get('doctrine')->getManager()->getRepository(User::class);
        $user = $em->findOneBy(['username' => 'daniil']);
        $user->setSuccessLogin(time());
        $this->container->get('doctrine')->getManager()->flush();
        return new JsonResponse(['success' => 'OK'], 200);
    }


    public function setContainer(ContainerInterface $container = null)
    {
        // TODO: Implement setContainer() method.
    }
}