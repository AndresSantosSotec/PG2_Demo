<?php
// routes.php
$router->post('/convert/excel', 'FileController@convertExcel');
$router->post('/convert/json', 'FileController@convertJson');
$router->post('/convert/csv', 'FileController@convertCsv');
?>