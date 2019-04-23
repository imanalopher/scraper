<?php
require_once __DIR__ . "/bootstrap.php";

use App\Entity\Admin;
use Symfony\Component\DomCrawler\Crawler;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "vendor/autoload.php";
require_once "support/web_browser.php";
require_once "support/tag_filter.php";

// Retrieve the standard HTML parsing array for later use.
$htmloptions = TagFilter::GetHTMLOptions();
//
$web = new WebBrowser(['extractforms' => true]);
$result = $web->Process($_ENV['BASE_URL']);

require_once "authentication.php";
sleep(2);
require_once "authenticationAsAdmin.php";
sleep(2);

$web->Process($resultAuthAsAdminForm['url']);
sleep(2);

$web->Process($_ENV['ADMIN_URL']);
sleep(2);

## GET Length data
$jsonAdmins = $web->Process($_ENV['ADMIN_JSON_URL']);
$jsonAdmins = json_decode($jsonAdmins['body'], true);
$totalAdmins = $jsonAdmins['iTotalRecords'] ?? PHP_INT_MAX;
sleep(2);

## GET Admin
$jsonUrl = "?data_tables=true&sEcho=1&iColumns=4&sColumns=%2C%2C%2C&iDisplayStart=0&iDisplayLength=$totalAdmins&mDataProp_0=0&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=1&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=2&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=false&mDataProp_3=3&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1";
$jsonAdminResult = $web->Process($_ENV['ADMIN_JSON_URL'].$jsonUrl);
$allJsonAdmins = json_decode($jsonAdminResult['body'], true)['aaData'];
sleep(2);

## Save Admin
foreach ($allJsonAdmins as $jsonAdmin) {
    $admin = new Admin();

    $crawler = new Crawler($jsonAdmin[0]);
    $name = $crawler->filterXPath("//b")->getNode(0)->nodeValue;

    $admin->setName($name);
    $admin->setEmail($jsonAdmin[1]);

    $roles = str_replace("<br>", '', $jsonAdmin[2]);
    $roles = explode(",", $roles);
    $admin->setRoles($roles);

    $em->persist($admin);
    $em->flush();

    sleep(1);
}