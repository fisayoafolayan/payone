<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Payone;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface PayoneConstants
{
    const COUNTRY_AT = 'AT';
    const COUNTRY_DE = 'DE';
    const COUNTRY_NL = 'NL';
    const COUNTRY_CH = 'CH';

    const PROVIDER_NAME = 'Payone';
    const PAYONE = 'PAYONE';
    const PAYONE_CREDENTIALS = 'PAYONE_CREDENTIALS';
    const PAYONE_CREDENTIALS_ENCODING = 'PAYONE_CREDENTIALS_ENCODING';
    const PAYONE_PAYMENT_GATEWAY_URL = 'PAYONE_PAYMENT_GATEWAY_URL';
    const PAYONE_CREDENTIALS_KEY = 'PAYONE_CREDENTIALS_KEY';
    const PAYONE_CREDENTIALS_MID = 'PAYONE_CREDENTIALS_MID';
    const PAYONE_CREDENTIALS_AID = 'PAYONE_CREDENTIALS_AID';
    const PAYONE_CREDENTIALS_PORTAL_ID = 'PAYONE_CREDENTIALS_PORTAL_ID';
    const PAYONE_REDIRECT_SUCCESS_URL = 'PAYONE_REDIRECT_SUCCESS_URL';
    const PAYONE_REDIRECT_ERROR_URL = 'PAYONE_REDIRECT_ERROR_URL';
    const PAYONE_REDIRECT_BACK_URL = 'PAYONE_REDIRECT_BACK_URL';

    // Paypal express checkout state machine name
    const PAYMENT_METHOD_PAYPAL_EXPRESS_CHECKOUT_STATE_MACHINE = 'payonePaypalExpressCheckout';

    const PAYONE_STANDARD_CHECKOUT_ENTRY_POINT_URL = 'PAYONE_STANDARD_CHECKOUT_ENTRY_POINT_URL';
    const PAYONE_EXPRESS_CHECKOUT_FAILURE_URL = 'PAYONE_EXPRESS_CHECKOUT_PROJECT_FAILURE_URL';
    const PAYONE_EXPRESS_CHECKOUT_BACK_URL = 'PAYONE_EXPRESS_CHECKOUT_PROJECT_BACK_URL';

    const PAYONE_EMPTY_SEQUENCE_NUMBER = 'PAYONE_EMPTY_SEQUENCE_NUMBER';

    const PAYONE_TXACTION_APPOINTED = 'appointed';

    const PAYONE_MODE = 'MODE';
    const PAYONE_MODE_TEST = 'test';
    const PAYONE_MODE_LIVE = 'live';

    /**
     * Path to bundle glossary file.
     */
    const GLOSSARY_FILE_PATH = 'Business/Internal/glossary.yml';

    const HOST_YVES = 'HOST_YVES';
}
