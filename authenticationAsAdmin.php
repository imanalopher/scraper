<?php

## Authentication AS Admin ***

/** @var WebBrowserForm $authAsAdminForm */
$authAsAdminForm = $authenticationResult['forms'][0];

$authAsAdminForm->RemoveFormField('Persona::SonderCenterAdmin');
$authAsAdminForm->RemoveFormField('Persona::SonderCenterFranchisee');

// Submit the form.
$authAsAdminFormRequest = $authAsAdminForm->GenerateFOrmRequest();

$options = $authAsAdminFormRequest['options'];
$resultAuthAsAdminForm = $web->Process($authAsAdminFormRequest["url"], $options);