<?php

/**
 * The endpoint dispatcher
 *
 * If you want to add a new endpoint, like infoarena.ro/my_endpoint this is
 * where you wanna come
 */
final class EndpointDispatcher {
    private $map;

    /**
     * We build the map here
     *
     * You can build up the endpoints recursivly
     *
     * @return this
     */
    public function __construct() {
        $this->map = array(
            '/' => 'HomeController',
            "(w|wiki)/" => 'WikiDefaultController');
    }

    /**
     * Given the request maps its path to a controller
     *
     * Also returns a condensed request, which retains only a smaller part of
     * the request, the part that wasn't matched
     *
     * @param Request $request
     * @return array(string, Request)
     */
    public function getController(Request $request) {
        list($controller, $new_request) =
            $this->tryToMap($request, $this->map);

        if ($controller === null) {
            return array('404Controller', $request);
        }

        return array($controller, $new_request);
    }

    /**
     * Internal functions which tries to map a request with some row from a
     * given map
     *
     * @param Request $request
     * @param array $map
     * @return array(string, Request)
     */
    private function tryToMap(Request $request, array $map) {
        foreach ($map as $prefix => $controller_map) {
            $matches = null;
            if (!preg_match('#^' . $prefix . '#',
                            $request->getPath(),
                            $matches)) {
                continue;
            }

            $partial_request =
                $request->cloneWithDifferentPath(
                    StringUtils::nonFalsesubstr(
                        $request->getPath(),
                        strlen($matches[0])));

            if (!is_array($controller_map)) {
                return array($controller_map, $partial_request);
            }

            list($controller, $new_request) =
                $this->tryToMap($partial_request, $controller_map);
            if ($controller != null) {
                return array($controller, $new_request);
            }
        }

        return array(null, null);
    }
}
