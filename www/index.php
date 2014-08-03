<?php
include_once '../define.php';
// $request=$_REQUEST['data'];
// $command=$request['cmd'];
define("MOOPHP_DATA_DIR", PATH_CONTROL);
define("MOOPHP_TEMPLATE_DIR", PATH_VIEW);


require_once PATH_ROOT."framework/MooPHP/MooPHP.php";


include( Mootemplate( 'help' ) );

