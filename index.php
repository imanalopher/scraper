<?php

require_once "support/web_browser.php";
require_once "support/tag_filter.php";
require_once __DIR__ . "/bootstrap.php";

// Retrieve the standard HTML parsing array for later use.
$htmloptions = TagFilter::GetHTMLOptions();

// Retrieve a URL (emulating Firefox by default).
$web = new WebBrowser(['extractforms' => true]);
$result = $web->Process($_ENV['BASE_URL']);

require_once "authentication.php";
sleep(2);
require_once "authenticationAsAdmin.php";

$web->Process($resultAuthAsAdminForm["url"]);
sleep(2);

## GET Length data
$jsonProviders = $web->Process($_ENV['PROVIDER_JSON_URL']);
$jsonProviders = json_decode($jsonProviders['body'], true);
$totalProviders = $jsonProviders['iTotalRecords'] ?? 999999;

## GET Provider JSON File

sleep(2);
$jsonUrl = $_ENV['PROVIDER_JSON_URL'] . "?data_tables=true&sEcho=2&iColumns=5&sColumns=%2C%2C%2C%2C&iDisplayStart=50&iDisplayLength=$totalProviders&mDataProp_0=0&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=1&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=2&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=false&mDataProp_3=3&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&mDataProp_4=4&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1&_=1553868594561";
$jsonProviders = json_decode($web->Process($jsonUrl)['body'], true)['aaData'];

sleep(2);

foreach ($jsonProviders as $jsonProvider) {
    echo $jsonProvider[1];
}
