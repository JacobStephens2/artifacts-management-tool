<?php

require_once('../artifacts_private/environment_variables.php');

require_once('../artifacts_private/vendor/autoload.php');

require_once('../artifacts_private/auth_functions.php');

include_once('database_functions.php');
$database = db_connect();

require_once('classes/DatabaseObject.class.php');
DatabaseObject::set_database($database);

// Classes that extend DatabaseObject
require_once('classes/Artifact.class.php');

?>