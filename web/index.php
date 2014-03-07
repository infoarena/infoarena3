<?php

require_once dirname(dirname(__FILE__))."/src/env/InfoarenaEnvironment.php";

InfoarenaEnvironment::start();

$original_exception = null; // if it crashes when we try to print the 500 page
                            // we should have a backup
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
        $view = $controller->execute();
        ob_start();
        echo $view->render();
        $output = ob_get_clean();
        echo $output;
    } catch(Exception $e) {
        $original_exception = $e;
        ob_get_clean(); // we don't care
        $controller = newv(
            '500Controller',
            array(
                InfoarenaEnvironment::getDatabaseConnection(),
                $request,
                $session));
       $view = $controller->execute();
       ob_start();
       echo $view->render();
       $output = ob_get_clean();
       echo $output;
    }

    // FIXME: uncomment this line too
    // $write_guard->dispose();
} catch (BasicException $e) {
    // this is bad, it crashed when we tried to display the 500 page
    ob_get_clean();
    if ($original_exception !== null) {
        // the original exception is more meaningful
        InfoarenaEnvironment::crash(
            $e->getMessage() . "\n" . $original_exception->getMessage(), // both
            ($original_exception instanceof BasicException ? // the original if
                $original_exception->getUserMessage() :      // possible
                $e->getUserMessage()));
    } else {
    InfoarenaEnvironment::crash(
        $e->getMessage(), $e->getUserMessage());
    }
} catch (Exception $e) {
    ob_get_clean();
    $message = $e->getMessage();
    if ($original_exception !== null) {
        $message .= "\n" . $original_exception->getMessage();
    }

    if ($original_exception instanceof BasicException) {
        InfoarenaEnvironment::crash(
            $message, $original_exception->getUserMessage());
    } else {
        InfoarenaEnvironment::crash($message);
    }
}
