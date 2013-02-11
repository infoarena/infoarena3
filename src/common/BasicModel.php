<?php

/**
 * BasicModel from which all Model Types should inherit
 */
abstract class BasicModel {
    private $connection;

    /**
     * Constructor for the basic model
     *
     * @param AphrontDatabaseConnection $_connection
     * @return void
     */
    public function __construct(AphrontDatabaseConnection $_connection) {
        $connection = $_connection;
    }
}
