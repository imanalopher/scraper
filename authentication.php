<?php

## Authentication

/** @var WebBrowserForm $authenticationForm */
$authenticationForm = $result['forms'][0];

$authenticationForm->SetFormValue('user[email]', $_ENV['EMAIL']);
$authenticationForm->SetFormValue('user[password]', $_ENV['PASSWORD']);

// Submit the form.
$authenticationFormRequest = $authenticationForm->GenerateFormRequest();

$options = $authenticationFormRequest["options"];
$authenticationResult = $web->Process($authenticationFormRequest["url"], $options);
