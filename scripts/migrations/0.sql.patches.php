<?php

require_once dirname(dirname(__FILE__))."/__init_script__.php";

$connection = InfoarenaEnvironment::getDatabaseConnection();

$model = new ScriptModel($connection);

$model->createTable(
    'sql_patches',
    array(
        'name CHAR(64) NOT NULL'
        ),
    '(name)');
