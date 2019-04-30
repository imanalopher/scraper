<?php
require_once __DIR__ . "/bootstrap.php";

use App\Entity\Provider;
use Symfony\Component\DomCrawler\Crawler;

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$providerList = [];

$userList = $web->Process($resultAuthAsAdminForm['url']);
sleep(2);

$providersList = $web->Process($_ENV['PROVIDER_URL']);
sleep(2);

## GET Length data
$jsonProviders = $web->Process($_ENV['PROVIDER_JSON_URL']);
$jsonProviders = json_decode($jsonProviders['body'], true);
$totalProviders = $jsonProviders['iTotalRecords'] ?? 999999;
sleep(2);

## GET Provider JSON File
$jsonUrl = $_ENV['PROVIDER_JSON_URL'] . "?data_tables=true&sEcho=2&iColumns=5&sColumns=%2C%2C%2C%2C&iDisplayStart=50&iDisplayLength=$totalProviders&mDataProp_0=0&sSearch_0=&bRegex_0=false&bSearchable_0=true&bSortable_0=true&mDataProp_1=1&sSearch_1=&bRegex_1=false&bSearchable_1=true&bSortable_1=true&mDataProp_2=2&sSearch_2=&bRegex_2=false&bSearchable_2=true&bSortable_2=false&mDataProp_3=3&sSearch_3=&bRegex_3=false&bSearchable_3=true&bSortable_3=false&mDataProp_4=4&sSearch_4=&bRegex_4=false&bSearchable_4=true&bSortable_4=false&sSearch=&bRegex=false&iSortCol_0=0&sSortDir_0=asc&iSortingCols=1&_=1553868594561";
$jsonProviders = json_decode($web->Process($jsonUrl)['body'], true)['aaData'];
sleep(2);

foreach ($jsonProviders as $providerItem) {

    $provider = new Provider();

    $crawler = new Crawler($providerItem[4]);
    $href = $crawler->filterXPath("//a")->getNode(0)->getAttribute('href');

    $providerForm = $web->Process($_ENV['BASE_URL'] . $href);

    /** @var WebBrowserForm $form */
    $form = $providerForm['forms'][0];

    foreach ($form->fields as $field) {
        switch ($field['id']) {
            case 'user_first_name':
                $provider->setFirstName($field['value']);
                break;

            case 'user_middle_name':
                $provider->setMiddleName($field['value']);
                break;

            case 'user_last_name':
                $provider->setLastName($field['value']);
                break;

            case 'user_birth_date':
                if (strlen($field['value']))
                    $provider->setBirthdate(new \DateTime($field['value']));
                break;

            case 'user_email':
                if (strlen($field['value']))
                    $provider->setEmail($field['value']);
                break;

            case 'user_phone_number':
                $provider->addPhone($field['value']);
                break;

            case 'user_cellphone_number':
                if (strlen($field['value']))
                    $provider->addPhone($field['value']);
                break;

            case 'user_mailing_address':
                if (strlen($field['value']))
                    $provider->addMailingAddress($field['value']);
                break;

            case 'user_mailing_address_line2':
                if (strlen($field['value']))
                    $provider->addMailingAddress($field['value']);
                break;

            case 'user_mailing_city':
                $provider->setCity($field['value']);
                break;

            case 'user_mailing_state':
                $provider->setState($field['options'][$field['value']]);
                break;

            case 'user_mailing_postal_code':
                $provider->setZip($field['value']);
                break;

            case 'user_time_zone':
                $provider->setTimezone($field['options'][$field['value']]);
                break;
        }
    }
    $provider->setProviderId(explode("/", $href)[4]);

    $em->persist($provider);
    $em->flush();

    echo $href . " - " . $provider->getEmail() . "\n";

    sleep(1);
}