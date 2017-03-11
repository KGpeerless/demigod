<?php
namespace AppBundle\Handler;

use Mcrst\Helper\Mobile;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, LogoutSuccessHandlerInterface
{
    use TargetPathTrait;

    protected $authorizationChecker;
    protected $router;
    protected $options;
    protected $providerKey;
    protected $defaultOptions = [
        'always_use_default_target_path' => false,
        'default_target_path'            => '/',
        'login_path'                     => '/login',
        'target_path_parameter'          => '_target_path',
        'use_referer'                    => false,
    ];

    /**
     * Constructor.
     *
     * @param array $options Options for processing a successful authentication attempt
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker,
                                Router $router,
                                array $options = [])
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router               = $router;
        $this->setOptions($options);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        if (!$url = $this->determineTargetUrl($request)) {
            switch (true) {
                case $this->authorizationChecker->isGranted('ROLE_ADMIN'):
                    $url = $this->router->generate(
                        'homepage');
                    break;
                default:
                    $url = $this->options['default_target_path'];
            }
        }

        $response = new RedirectResponse($url);
        $response
            ->headers
            ->setCookie(new Cookie("OTHER", $token->getUser()->getApiKey()));

        return $response;
    }

    public function onLogoutSuccess(Request $request)
    {
        $response = new RedirectResponse($this->options['default_target_path']);
        $response->headers->clearCookie("OTHER");

        return $response;
    }

    /**
     * Sets the options.
     *
     * @param array $options An array of options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($this->defaultOptions, $options);
    }

    /**
     * Get the provider key.
     *
     * @return string
     */
    public function getProviderKey()
    {
        return $this->providerKey;
    }

    /**
     * Set the provider key.
     *
     * @param string $providerKey
     */
    public function setProviderKey($providerKey)
    {
        $this->providerKey = $providerKey;
    }

    /**
     * Builds the target URL according to the defined options.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function determineTargetUrl(Request $request)
    {
        if ($targetUrl = ParameterBagUtils::getRequestParameterValue($request, $this->options['target_path_parameter'])) {
            return $targetUrl;
        }

        if (null !== $this->providerKey && $targetUrl = $this->getTargetPath($request->getSession(), $this->providerKey)) {
            $this->removeTargetPath($request->getSession(), $this->providerKey);

            return $targetUrl;
        }

        if ($this->options['use_referer'] && ($targetUrl = $request->headers->get('Referer')) && $targetUrl !== $this->router->generate($this->options['login_path'], [], 0)) {
            return $targetUrl;
        }

        return null;
    }
}