<?php

/**
 * Basic Controller from which all controllers should inherit
 */
abstract class BasicController {
    protected $connection;
    protected $request;
    protected $session;

    /**
     * Constructor for the basic controller
     *
     * This is the necessary information for a controller
     *
     * @param AphrontDatabaseConnection $connection
     * @param Request $request
     * @param Session $session
     * @return
     */
    public function __construct(
        AphrontDatabaseConnection $connection,
        Request $request,
        Session $session) {
        $this->connection = $connection;
        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Starts the controller to further render the data/
     *
     * @return void
     */
    abstract public function execute();
}
