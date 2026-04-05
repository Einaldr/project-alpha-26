<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Mailtrap\Mime\MailtrapEmail;
use Symfony\Component\Mime\Address;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('send-test-mail', function () 
{
    $email = (new MailtrapEmail())
        ->from(new Address(config('mail.from.address'), config('mail.from.name')))
        ->to(new Address(config('mail.test.testing-mail')))
        ->subject('You are awesome!')
        ->category('Integration Test')
        ->text('Congrats for sending test email with Mailtrap!')
    ;

    $response = MailtrapClient::initSendingEmails(
        apiKey: config('mail.mailtrap.api-key')
    )->send($email);

    var_dump(ResponseHelper::toArray($response));
})->purpose('Send Mail');