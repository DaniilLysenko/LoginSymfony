<?php

namespace App\Security\Authentication\Handler;

use App\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AuthFailureHandler implements AuthenticationFailureHandlerInterface, ContainerAwareInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $em = $this->container->get('doctrine')->getManager()->getRepository(User::class);
        $user = $em->findOneBy(['username' => $request->get('_username')]);
        if ($user) {
            $user->addFailedLogins(time());
            $this->container->get('doctrine')->getManager()->flush();
        }
        return new JsonResponse(['err' => $exception->getMessage()], 404);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        // TODO: Implement setContainer() method.
    }
}