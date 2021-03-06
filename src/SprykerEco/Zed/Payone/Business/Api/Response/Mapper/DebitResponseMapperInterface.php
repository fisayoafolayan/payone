<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Payone\Business\Api\Response\Mapper;

use SprykerEco\Zed\Payone\Business\Api\Response\Container\DebitResponseContainer;

interface DebitResponseMapperInterface extends ResponseMapperInterface
{
    /**
     * @const string NAME
     */
    public const NAME = 'DEBIT';

    /**
     * @param \SprykerEco\Zed\Payone\Business\Api\Response\Container\DebitResponseContainer $responseContainer
     *
     * @return \Generated\Shared\Transfer\DebitResponseTransfer
     */
    public function getDebitResponseTransfer(DebitResponseContainer $responseContainer);
}
