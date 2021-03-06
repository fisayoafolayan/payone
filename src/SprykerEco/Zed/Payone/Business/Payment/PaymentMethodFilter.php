<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Payone\Business\Payment;

use ArrayObject;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Payone\PayoneConfig;

class PaymentMethodFilter implements PaymentMethodFilterInterface
{
    protected const PAYONE_PAYMENT_METHOD = 'payone';
    protected const PAYONE_SCORE_GREEN = 'G';
    protected const PAYONE_SCORE_YELLOW = 'Y';
    protected const PAYONE_SCORE_RED = 'R';
    protected const PAYONE_SCORE_UNKNOWN = 'U';
    protected const CONFIG_METHOD_PART_GET = 'get';
    protected const CONFIG_METHOD_PART_AVAILABLE_PAYMENT_METHODS = 'ScoreAvailablePaymentMethods';

    /**
     * @var \SprykerEco\Zed\Payone\PayoneConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\Payone\PayoneConfig $config
     */
    public function __construct(PayoneConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodsTransfer $paymentMethodsTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function filterPaymentMethods(PaymentMethodsTransfer $paymentMethodsTransfer, QuoteTransfer $quoteTransfer): PaymentMethodsTransfer
    {
        $availableMethods = $this->getAvailablePaymentMethods($quoteTransfer);

        $result = new ArrayObject();

        foreach ($paymentMethodsTransfer->getMethods() as $paymentMethod) {
            if ($this->isPaymentMethodPayone($paymentMethod) && !$this->isAvailable($paymentMethod, $availableMethods)) {
                continue;
            }
            $result->append($paymentMethod);
        }
        $paymentMethodsTransfer->setMethods($result);

        return $paymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string[]
     */
    protected function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): array
    {
        $score = $quoteTransfer->getConsumerScore();

        $method = static::CONFIG_METHOD_PART_GET .
            ucfirst($score) .
            static::CONFIG_METHOD_PART_AVAILABLE_PAYMENT_METHODS;

        if (method_exists($this->config, $method)) {
            return $this->config->$method();
        }

        return $this->config->getUScoreAvailablePaymentMethods();
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     * @param string[] $availableMethods
     *
     * @return bool
     */
    protected function isAvailable(PaymentMethodTransfer $paymentMethodTransfer, $availableMethods): bool
    {
        return in_array($paymentMethodTransfer->getMethodName(), $availableMethods);
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return bool
     */
    protected function isPaymentMethodPayone(PaymentMethodTransfer $paymentMethodTransfer): bool
    {
        return strpos($paymentMethodTransfer->getMethodName(), static::PAYONE_PAYMENT_METHOD) !== false;
    }
}
