<?php

use App\Entity\Avatar;
use App\Entity\Provider;
use Symfony\Component\DomCrawler\Crawler;

require_once __DIR__ . "/bootstrap.php";
require_once "vendor/autoload.php";
require_once "support/web_browser.php";
require_once "support/tag_filter.php";

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

function DownloadFileCallback($response, $data, $opts)
{
    if ($response["code"] == 200)
    {
        $size = ftell($opts);
        fwrite($opts, $data);

        if ($size % 1000000 > ($size + strlen($data)) % 1000000)  echo ".";
    }

    return true;
}

foreach($jsonProviders as $providerItem) {
    $providerId = explode('/', $providerItem[4])[4];

    echo "User Id = " . $providerId . "\n";
    $providerProfileUrl = $_ENV['BASE_URL'] . "/admin/user_management/users/$providerId/profile_reviews/$providerId";

    $provider = $em->getRepository(Provider::class)->findOneBy(['providerId' => $providerId]);
    $avatar = new Avatar();
    if($provider instanceof Provider) {
        $avatar = $em->getRepository(Avatar::class)->findOneBy(['provider' => $provider->getId()]);
    }

    if (!$avatar instanceof Avatar) {
        $providerProfile = $web->Process($providerProfileUrl);

        $avatar = new Avatar();
        $avatar->setProvider($provider);
        $avatar->setAccess(false);
        $avatar->setStatus(intval($providerProfile["response"]["code"]));

        if ($providerProfile["response"]["code"] === "200" && $providerProfile['success']) {
            $html = TagFilter::Explode($providerProfile["body"], $htmloptions);

            $imgResult = $html->Find("div.col-md-3");
            if ($imgResult['success'] === true) {
                foreach ($imgResult['ids'] as $id) {
                    $div = $html->GetOuterHTML($id);

                    $crawler = new Crawler();
                    $crawler->addHtmlContent($div);
                    $src = $crawler->filterXPath("//img")->getNode(0)->getAttribute("src");

                    $ext = pathinfo($src, PATHINFO_EXTENSION);
                    $ext = strtolower(explode("?", $ext)[0]);
                    $fp = fopen("avatars/$providerId.$ext", "wb");
                    $options = [
                        "read_body_callback" => "DownloadFileCallback",
                        "read_body_callback_opts" => $fp
                    ];

                    $result = $web->Process($src, $options);
                    fclose($fp);

                    $avatar->setAccess(true);
                    $avatar->setImg("avatars/$providerId.$ext");
                    $avatar->setUrl($src);

                    echo $src;
                }
            }
        }

        $em->persist($avatar);
        $em->flush();

        echo "\n\n";
        sleep(2);
    }
}

