<?php
	/**
	 * Created by PhpStorm.
	 * User: Iceman
	 * Date: 01.10.2018
	 * Time: 19:00
	 */

	namespace AppBundle\EventListener;

	use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
	use Symfony\Component\Routing\RouterInterface;
	use Symfony\Component\HttpFoundation\RedirectResponse;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;
	use FOS\UserBundle\Model\User;

	class RedirectUserListener
	{
		private $tokenStorage;
		private $router;

		public function __construct(TokenStorageInterface $t, RouterInterface $r)
		{
			$this->tokenStorage = $t;
			$this->router = $r;
		}

		public function onKernelRequest(GetResponseEvent $event)
		{
			if ($this->isUserLogged() && $event->isMasterRequest())
			{
				$currentRoute = $event->getRequest()->attributes->get('_route');
				if($this->isAuthenticatedUserOnAnonymousPage($currentRoute))
				{
					$response = new RedirectResponse($this->router->generate('homepage'));
					$event->setResponse($response);
				}
			}
		}

		private function isUserLogged()
		{
			$token = $this->tokenStorage->getToken();
			if(!empty($token))
				return $token->getUser() instanceof User;

			return false;
		}

		private function isAuthenticatedUserOnAnonymousPage($currentRoute)
		{
			return in_array(
				$currentRoute,
				['fos_user_security_login', 'fos_user_resetting_request', 'app_user_registration']
			);
		}

	}