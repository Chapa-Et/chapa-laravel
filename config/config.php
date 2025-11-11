<?php

/*
 * This file is part of the Chapa Laravel package.
 *
 * Kidus Yared - @kidus363 <kidusy@chapa.co>
 *
 * 
 */
return [


    /**
     * Secret Key: Your Chapa secretKey. Sign up on https://dashboard.chapa.co/ to get one from your settings page
     *
     */
    'secretKey' => env('CHAPA_SECRET_KEY'),


    /**
     * Webhook Secret: Your Chapa webhook secret for validating incoming events.
     */
    'webhookSecret' => env('CHAPA_WEBHOOK_SECRET'),

];