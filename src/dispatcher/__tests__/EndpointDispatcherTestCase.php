<?php

final class EndpointDispatcherTestCase extends ArcanistPhutilTestCase {
    public function testDispatchNormalRequest() {
        $dispatcher = new EndpointDispatcher();
        $request = new Request('infoarena.ro', 'wiki/test/something');

        list($controller, $new_request) = $dispatcher->getController($request);

        $this->assertEqual(
            'WikiDefaultController',
            $controller,
            'Controller not set correctly');
        $this->assertEqual(
            'test/something/',
            $new_request->getPath(),
            'Path not simplified');
    }

    public function testDispatchBlankRequest() {
        $dispatcher = new EndpointDispatcher();
        $request = new Request('infoarena.ro', '');

        list($controller, $new_request) = $dispatcher->getController($request);

        $this->assertEqual(
            'HomeController',
            $controller,
            'Controller not set correctly');
        $this->assertEqual(
            '/',
            $new_request->getPath(),
            'Path not set correctly');
    }

    public function testDispatchNonMatchingRequest() {
        $dispatcher = new EndpointDispatcher();
        $request = new Request('infoarena.ro', '/random_wrong_path/');

        list($controller, $new_request) = $dispatcher->getController($request);

        $this->assertEqual(
            '404Controller',
            $controller,
            'Controller not set correctly');
        $this->assertEqual(
            'random_wrong_path/',
            $new_request->getPath(),
            'Path not set correctly');
    }
}
