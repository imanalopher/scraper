<?php
require_once __DIR__ . "/bootstrap.php";
require_once "vendor/autoload.php";
require_once "support/web_browser.php";
require_once "support/tag_filter.php";

use App\Entity\Provider;
use App\Entity\Specialties;
use Symfony\Component\DomCrawler\Crawler;

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$web->Process($_ENV['PROVIDER_URL']);
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

foreach($jsonProviders as $providerItem) {
    $providerId = explode('/', $providerItem[4])[4];

    echo "User Id = " . $providerId . "\n";

    $provider = $em->getRepository(Provider::class)->findOneBy(['providerId' => $providerId]);
    $specialities = null;
    if ($provider instanceof Provider) {
        $specialities = $em->getRepository(Specialties::class)->findOneBy(['provider' => $provider->getId()]);
    }

    if (!$specialities instanceof Specialties) {

        $specialitiesUrl = $_ENV['BASE_URL'] . "/admin/user_management/persona_saas/$providerId/specialties";

        $resultSpecialities = $web->Process($specialitiesUrl);
        if ($resultSpecialities["response"]["code"] === "200" && $resultSpecialities['success']) {
            $specialitiesForm = $resultSpecialities['forms'][0];
            foreach ($specialitiesForm->fields as $field) {
                $value = null;
                if ($field['id'] && strpos($field['name'], 'member_speciality') !== false) {
                    if ($field['type'] === 'select') {
                        $value = $field['options'][$field['value']];
                    } elseif ($field['type'] === 'input.checkbox' && $field['checked']) {
                        $crawler = new Crawler($field['hint']);
                        $value = $crawler->getNode(0)->textContent;
                    }

                    if (!is_null($value)) {
                        $provider = $em->getRepository(Provider::class)->findOneBy(['providerId' => $providerId]);
                        $specialities = new Specialties();
                        $specialities->setProvider($provider);
                        $specialities->setSpecialty($value);

                        $em->persist($specialities);
                        $em->flush();

                        echo $field['id'] . " = " . $value . "\n";

                        unset($specialities);
                        unset($resultSpecialities);
                    }
                }
            }
        }
        echo "\n\n";
        sleep(2);
    }
}
