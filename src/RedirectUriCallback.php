<?php

namespace ZfcUserRedirectUri;

use Zend\Mvc\Application;
use Zend\Router\RouteInterface;
use Zend\Http\PhpEnvironment\Response;
use ZfcUser\Options\ModuleOptions;
use Zend\Router\RouteMatch;
use Zend\Http\Request;

/**
 * Can redirect to the URI given during login.
 * N.B. Don't extend ZfcUser version since its members are all private.
 */
class RedirectUriCallback {

    /**
     * @var RouteInterface
     */
    protected $router;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var ModuleOptions
     */
    protected $options;

    /**
     * @param Application $application
     * @param RouteInterface $router
     * @param ModuleOptions $options
     */
    public function __construct(
    Application $application, RouteInterface $router, ModuleOptions $options
    ) {
        $this->router = $router;
        $this->application = $application;
        $this->options = $options;
    }

    /**
     * @return Response
     */
    public function __invoke() {
        $routeMatch = $this->application->getMvcEvent()->getRouteMatch();
        $redirect = $this->getRedirect($routeMatch->getMatchedRouteName(),
        $this->getRedirectRouteFromRequest());

        $response = $this->application->getResponse();
        $response->getHeaders()->addHeaderLine('Location', $redirect);
        $response->setStatusCode(302);
        return $response;
    }

    protected function getRedirectRouteFromRequest() {
        $request = $this->application->getRequest();
        $redirect = $request->getQuery('redirect')? : $request->getPost('redirect');
        return $this->getSafeRedirect($redirect);
    }

    /**
     * Sanitize URI: through router and back.
     *
     * @param $uri
     * @return mixed
     */
    protected function getSafeRedirect($uri) {
        if (!$uri) {
            return null;
        }
        try {
            if ($queryString = parse_url($uri, PHP_URL_QUERY)) {
                $queryString = "?{$queryString}";
            }

            $request = new Request;
            $request->setUri($uri);
            $routeMatch = $this->router->match($request);
            if ($routeMatch instanceof RouteMatch) {
                return $this->router->assemble(
                $routeMatch->getParams(),
                ['name' => $routeMatch->getMatchedRouteName()]
                ) . $queryString;
            }
        }
        // @codeCoverageIgnoreStart
        catch (\Exception $e) {

        }
        // @codeCoverageIgnoreEnd
        return null;
    }

    /**
     * Returns the url to redirect to based on current route.
     * If $redirect is set and the option to use redirect is set to true, it will return the $redirect url.
     *
     * @param string $currentRoute
     * @param mixed $redirect
     * @return mixed
     */
    protected function getRedirect($currentRoute, $redirect = null) {
        $redirect = $this->getSafeRedirect($redirect);
        if (!$this->options->getUseRedirectParameterIfPresent()) {
            // @codeCoverageIgnoreStart
            $redirect = false;
        }
        // @codeCoverageIgnoreEnd

        if ($redirect) {
            return $redirect;
        }

        $route = 'zfcuser';
        switch ($currentRoute) {
            case 'zfcuser/register':
            case 'zfcuser/login':
                $route = $this->options->getLoginRedirectRoute();
                break;
            case 'zfcuser/logout':
                $route = $this->options->getLogoutRedirectRoute();
                break;
        }
        return $this->router->assemble([], ['name' => $route]);
    }

}
