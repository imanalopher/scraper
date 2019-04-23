<?php

use App\Entity\Saas;
use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . "/bootstrap.php";

require_once "vendor/autoload.php";
require_once "support/web_browser.php";
require_once "support/tag_filter.php";

// Retrieve the standard HTML parsing array for later use.
$htmloptions = TagFilter::GetHTMLOptions();

$web = new WebBrowser(['extractforms' => true]);
$result = $web->Process($_ENV['BASE_URL']);

require_once "authentication.php";
sleep(2);
require_once "authenticationAsAdmin.php";
sleep(2);

$web->Process($resultAuthAsAdminForm['url']);
sleep(2);

$web->Process($_ENV['SAAS_URL']);
sleep(2);

## GET Length data
$jsonSaas = $web->Process($_ENV['SAAS_JSON_URL']);
$jsonSaas = json_decode($jsonSaas['body'], true);
$totalSaasRecords = $jsonSaas['iTotalRecords'] ?? PHP_INT_MAX;
sleep(2);

## GET ALL SAAS
$jsonUrl = "?data_tables=true&sEcho=1&iColumns=4&sColumns=%2C%2C%2C&iDisplayStart=0&iDisplayLength=$totalSaasRecords&mDataProp_0=0&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=1&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=2&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=false&mDataProp_3=3&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1";
$jsonSaasResult = $web->Process($_ENV['SAAS_JSON_URL'].$jsonUrl);
$allJsonSaas = json_decode($jsonSaasResult['body'], true)['aaData'];
sleep(2);

foreach ($allJsonSaas as $jsonSaas) {
    $saas = new Saas();

    $crawler = new Crawler($jsonSaas[3]);
    $href = $crawler->filterXPath("//a")->getNode(0)->getAttribute('href');

    $saasId = (int) filter_var($href, FILTER_SANITIZE_NUMBER_INT);

    $saas->setSaasId($saasId);

    $saasForm = $web->Process($_ENV['BASE_URL'] . $href);

    /** @var WebBrowserForm $form */
    $form = $saasForm['forms'][0];

    foreach ($form->fields as $field) {
        switch ($field['id']) {
            case 'user_first_name':
                $saas->setFirstName($field['value']);
                break;

            case 'user_middle_name':
                $saas->setMiddleName($field['value']);
                break;

            case 'user_last_name':
                $saas->setLastName($field['value']);
                break;

            case 'user_birth_date':
                if (strlen($field['value']))
                    $saas->setBirthdate(new \DateTime($field['value']));
                break;

            case 'user_email':
                if (strlen($field['value']))
                    $saas->setEmail($field['value']);
                break;

            case 'user_phone_number':
                $saas->addPhone($field['value']);
                break;

            case 'user_cellphone_number':
                if (strlen($field['value']))
                    $saas->addPhone($field['value']);
                break;

            case 'user_mailing_address':
                if (strlen($field['value']))
                    $saas->addMailingAddress($field['value']);
                break;

            case 'user_mailing_address_line2':
                if (strlen($field['value']))
                    $saas->addMailingAddress($field['value']);
                break;

            case 'user_mailing_city':
                $saas->setCity($field['value']);
                break;

            case 'user_mailing_state':
                $saas->setState($field['options'][$field['value']]);
                break;

            case 'user_mailing_postal_code':
                $saas->setZip($field['value']);
                break;

            case 'user_time_zone':
                $saas->setTimezone($field['options'][$field['value']]);
                break;
        }
    }

    $em->persist($saas);
    $em->flush();

    echo $href . " - " . $saas->getEmail() . "\n";

    sleep(1);
}
