<?php

final class RequestTestCase extends ArcanistPhutilTestCase {
    public function testRequestIsCorrectlyBuilt() {
        $request = null;
        try {
            $request = new Request('infoarena.ro', '/test/path/');
            $request->setProtocol('HTTPS')
                    ->setData(array("data1" => "Hello World!"))
                    ->setMethod("POST")
                    ->setArguments(array("action" => "test_action"));
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual(
            'infoarena.ro',
            $request->getHost(),
            'Host not set correctly');
        $this->assertEqual(
            'test/path/',
            $request->getPath(),
            'Path not set correctly');
        $this->assertEqual(
            'HTTPS',
            $request->getProtocol(),
            'Protocol not set correctly');

        $this->assertEqual(
            array("data1" => "Hello World!"),
            $request->getData(),
            'Data array not set correctly');
        $this->assertEqual(
            "Hello World!",
            $request->data('data1'),
            'Data not set correctly');
        $this->assertEqual(
            "Default string",
            $request->data('data2', 'Default string'),
            'Default data value not working');
        $this->assertEqual(
            null,
            $request->data('data2'),
            'Default data should be null when not given');

        $this->assertEqual(
            'POST',
            $request->getMethod(),
            'Method not set correctly');

        $this->assertEqual(
            array("action" => "test_action"),
            $request->getArguments(),
            "Arguments array not set correctly");
        $this->assertEqual(
            "test_action",
            $request->argument("action"),
            "Argument not set correctly");
        $this->assertEqual(
            "Default value",
            $request->argument("action2", "Default value"),
            "Default argument value not working");
        $this->assertEqual(
            null,
            $request->argument("action2"),
            "Default argument should be null when not given");
    }

    public function testRequestCloned() {
        $request = null;
        try {
            $request = new Request('infoarena.ro', '/test/path/');
            $request->setProtocol('HTTPS')
                    ->setData(array("data1" => "Hello World!"))
                    ->setMethod("POST")
                    ->setArguments(array("action" => "test_action"));
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual(
            'test/path/',
            $request->getPath(),
            'Path is not set correctly');

        $new_request = null;
        try {
            $new_request = $request->cloneWithDifferentPath('new/test/path/');
        } catch (Exception $e) {
            $this->assertFailure($e->getMessage());
        }

        $this->assertEqual(
            'test/path/',
            $request->getPath(),
            'Original request object path should not modify on alterPath');

        $this->assertEqual(
            'new/test/path/',
            $new_request->getPath(),
            'New request object path should modify on alterPath');

        $this->assertEqual(
            'infoarena.ro',
            $new_request->getHost(),
            'Host not carried over on alterPath');
        $this->assertEqual(
            'HTTPS',
            $new_request->getProtocol(),
            'Protocol not carried over on alterPath');
        $this->assertEqual(
            array("data1" => "Hello World!"),
            $new_request->getData(),
            'Data array not carried over on alterPath');

        $this->assertEqual(
            'POST',
            $new_request->getMethod(),
            'Method not carried over on alterPath');

        $this->assertEqual(
            array("action" => "test_action"),
            $new_request->getArguments(),
            "Argument array not carried over on alterPath");
    }
}
