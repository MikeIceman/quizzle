<?php

namespace AppBundle\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use FOS\UserBundle\Model\User;

/**
 * Class RedirectUserListener
 *
 * @package AppBundle\EventListener
 */
class RedirectUserListener
{
    private $tokenStorage;
    private $router;

    /**
     * RedirectUserListener constructor.
     *
     * @param TokenStorageInterface $t
     * @param RouterInterface $r
     */
    public function __construct(TokenStorageInterface $t, RouterInterface $r)
    {
        $this->tokenStorage = $t;
        $this->router = $r;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($this->isUserLogged() && $event->isMasterRequest()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            if ($this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
                $response = new RedirectResponse($this->router->generate('homepage'));
                $event->setResponse($response);
            }
        }
    }

    /**
     * @return bool
     */
    private function isUserLogged()
    {
        $token = $this->tokenStorage->getToken();
        if ($token !== null) {
            return $token->getUser() instanceof User;
        }

        return false;
    }

    /**
     * @param $currentRoute
     *
     * @return bool
     */
    private function isAuthenticatedUserOnAnonymousPage($currentRoute)
    {
        return in_array(
            $currentRoute,
            ['fos_user_security_login', 'fos_user_resetting_request', 'app_user_registration']
        );
    }
}
