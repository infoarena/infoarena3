<?php

require_once dirname(dirname(__FILE__))."/src/env/InfoarenaEnvironment.php";

InfoarenaEnvironment::start();

try {
    $request = InfoarenaEnvironment::getRequest();

    $dispatcher = new EndpointDispatcher();

    $session = InfoarenaEnvironment::getSession();

    list($controller, $condensed_request) =
        $dispatcher->getController($request);

    /*
     * FIXME: use proper security after we add a CSRF validator
     * Also wait until we add user logging in/logging out

    $csrf_validator =
        new CSRFChecker($request, $user);

    $write_guard =
        new AphrontWriteGuard(
            array($csrf_validator, 'validateCSRF'));
    */

    try {
        $controller = newv(
            $controller,
            array(
                InfoarenaEnvironment::getDatabaseConnection(),
                $condensed_request,
                $session));
        $controller->execute();
    } catch(Exception $e) {
        throw $e;
        $controller = newv(
            '500Controller',
            array(
                InfoarenaEnvironment::getDatabaseConnection(),
                $request,
                $session))
        ->execute();
    }

    // FIXME: uncomment this line too
    // $write_guard->dispose();
} catch (BasicException $e) {
    InfoarenaEnvironment::crash(
        $e->getMessage(), $e->getUserMessage());
} catch (Exception $e) {
    InfoarenaEnvironment::crash($e->getMessage());
}
