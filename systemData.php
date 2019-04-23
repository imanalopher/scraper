<?php

use App\Entity\MessageTemplate;
use App\Entity\Settings;
use App\Entity\Specialty;
use App\Entity\UnsubscribedPeople;
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

## GET All system data
$systemDataHTML = $web->Process($_ENV['SYSTEM_SETTINGS_URL']);

// Use TagFilter to parse the content.
$html = TagFilter::Explode($systemDataHTML["body"], $htmloptions);

// Retrieve a pointer object to the root node.
$root = $html->Get();

$result2 = $html->Filter($html->Find("tr"), "td");
$systemSettingUrls = [];
$exclusionUrls = [];

## Get all url's
foreach ($result2["ids"] as $id)
{
    $trs = $html->GetOuterHTML($id);

    $crawler = new Crawler();
    $crawler->addHtmlContent($trs);
    $links = $crawler->filterXPath("//td")->filterXPath("//a");

    foreach($links->getIterator() as $link) {

        $href = $link->getAttribute('href');
        if (in_array($href, ['/reference_data/specialties', '/unsubscribed_contacts', '/message_templates'])) {
            $exclusionUrls[] = $href;
        } else {
            $systemSettingUrls[] = $link->getAttribute('href');
        }
    }
}

## Exclusion Url`s
foreach ($exclusionUrls as $exclusionUrl) {
    echo $exclusionUrl . "\n";

    $settingPage = $web->Process($_ENV['BASE_URL'] . $exclusionUrl);

    if($settingPage['success']) {

        $settingPage = $web->Process($_ENV['BASE_URL'] . $exclusionUrl);
        $html = TagFilter::Explode($settingPage["body"], $htmloptions);

        $result2 = $html->Filter($html->Find("tr"), "td");

        // Retrieve a pointer object to the root node.
        $root = $html->Get();

        $crawler = new Crawler();

        $settingPageTitle = "";

        ## Check current url contains or not "reference_data"
        if ($exclusionUrl === "/reference_data/specialties") {
            $crawler->addHtmlContent($root->Find("div.page-header-nav h3")->current());
            $crawler->filter("div.pull-right")->each(function (Crawler $craw) {
                foreach ($craw as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            $settingPageTitle = $crawler->filterXPath("//h3")->getNode(0)->nodeValue;

            $jsonResponse = $web->Process($_ENV['SPECIALTIES_JSON_URL'] . PHP_INT_MAX);
            $specialtiesJson = json_decode($jsonResponse['body'], true)['aaData'];

            foreach ($specialtiesJson as $specJson) {
                $crawler = new Crawler($specJson[0]);
                $title = $crawler->getNode(0)->nodeValue;

                $type = $specJson[1];

                $crawler = new Crawler($specJson[2]);
                $orderNumber = $crawler->filterXPath("//span")->getNode(0)->nodeValue;

                $tag = $specJson[3];

                $crawler = new Crawler($specJson[4]);
                $href = $crawler->filterXPath("//a")->getNode(0)->getAttribute('href');

                $specialty = new Specialty();
                $specialty->setTitle($title);
                $specialty->setType($type);
                $specialty->setOrderNumber($orderNumber);

                $em->persist($specialty);
                $em->flush();
            }
        }

        ## Message Template
        if ($exclusionUrl === "/message_templates") {
            $crawler->addHtmlContent($root->Find("div.page-header-nav h3")->current());
            $crawler->filter("a.btn")->each(function (Crawler $craw) {
                foreach ($craw as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            $settingPageTitle = $crawler->filterXPath("//h3")->getNode(0)->nodeValue;
            foreach ($result2["ids"] as $id)
            {
                $trs = $html->GetOuterHTML($id);

                $crawler = new Crawler();
                $crawler->addHtmlContent($trs);
                $links = $crawler->filterXPath("//td");

                $tag = $links->getNode(0)->nodeValue;
                $description = $links->getNode(1)->nodeValue;

                $message = new MessageTemplate();
                $message->setTag($tag);
                $message->setDescription($description);

                $em->persist($message);
                $em->flush();
            }
        }

        ## Unsubscribed Contacts
        if ($exclusionUrl === "/unsubscribed_contacts") {
            $crawler->addHtmlContent($root->Find("div.main-body h3")->current());
            $crawler->filter("div.pull-right")->each(function (Crawler $craw) {
                foreach ($craw as $node) {
                    $node->parentNode->removeChild($node);
                }
            });

            $settingPageTitle = $crawler->filterXPath("//h3")->getNode(0)->nodeValue;

            foreach ($result2["ids"] as $id)
            {
                $trs = $html->GetOuterHTML($id);

                $crawler = new Crawler();
                $crawler->addHtmlContent($trs);
                $links = $crawler->filterXPath("//td");

                $phone = $links->getNode(0)->nodeValue;
                $email = $links->getNode(1)->nodeValue;

                $people = new UnsubscribedPeople();
                $people->setPhone($phone);
                $people->setEmail($email);

                $em->persist($people);
                $em->flush();
            }
        }

        sleep(rand(2, 5));
    }
}


## Show all url direct
foreach ($systemSettingUrls as $settingUrl) {

    $settingPage = $web->Process($_ENV['BASE_URL'].$settingUrl);

    if($settingPage['success']) {
        $html = TagFilter::Explode($settingPage["body"], $htmloptions);

        $ul2 = $html->Find("ul.pagination a");
        $paginations = [];
        $countPage = 1;

        if (count($ul2["ids"]) > 0) {
            $paginations[] = $settingUrl;
            foreach ($ul2["ids"] as $id) {
                $li = $html->GetOuterHTML($id);
                $aCrawler = new Crawler($li);
                $href = $aCrawler->filterXPath("//a")->getNode(0)->getAttribute('href');

                preg_match("/(\d+)/", $href, $match);
                $countPage = $countPage < $match[1] ? $match[1] : $countPage;
            }
        }

        for ($i = 1; $i < $countPage + 1; $i++) {
            $settingPage = $web->Process($_ENV['BASE_URL'].$settingUrl . "?page=" . $i);
            $html = TagFilter::Explode($settingPage["body"], $htmloptions);

            echo $settingUrl . "?page=" . $i . "\n";

            $result2 = $html->Filter($html->Find("tr"), "td");

            // Retrieve a pointer object to the root node.
            $root = $html->Get();

            $crawler = new Crawler();

            ## Check current url contains or not "reference_data"
            $h = 'h1';
            if (strpos($settingUrl, 'reference_data') !== false) {
                $h = 'h3';
            }

            if (strpos($settingUrl, 'form_wizards') !== false) {
                $crawler->addHtmlContent($root->Find("div.main-body $h")->current());
            }
            else {
                $crawler->addHtmlContent($root->Find("div.page-header $h")->current());
                $crawler->filter("div.pull-right")->each(function (Crawler $craw) {
                    foreach ($craw as $node) {
                        $node->parentNode->removeChild($node);
                    }
                });
            }

            $settingPageTitle = $crawler->filterXPath("//$h")->getNode(0)->nodeValue;
            foreach ($result2["ids"] as $id)
            {
                $trs = $html->GetOuterHTML($id);

                $crawler = new Crawler();
                $crawler->addHtmlContent($trs);
                $links = $crawler->filterXPath("//td");

                $setting = new Settings();
                $setting->setName($settingPageTitle);
                $setting->setTitle($links->getNode(0)->nodeValue);
                $setting->setTag($links->getNode(1)->nodeValue);

                $em->persist($setting);
                $em->flush();
            }
        }
        sleep(rand(2, 5));
    }
}