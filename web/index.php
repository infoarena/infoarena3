<?php

require_once dirname(dirname(__FILE__))."/src/env/InfoarenaEnvironment.php";

InfoarenaEnvironment::start();

try {
    $request = InfoarenaEnvironment::getRequest();

    $dispatcher = new EndpointDispatcher();

    list($controller, $condensed_request) =
        $dispatcher->getController($request);
} catch (BasicException $e) {
    InfoarenaEnvironment::crash(
        $e->getMessage(), $e->getUserMessage());
} catch (Exception $e) {
    InfoarenaEnvironment::crash($e->getMessage());
}
