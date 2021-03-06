<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Payone;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerEco\Yves\Payone\Dependency\Client\PayoneToCalculationBridge;
use SprykerEco\Yves\Payone\Dependency\Client\PayoneToCartBridge;
use SprykerEco\Yves\Payone\Dependency\Client\PayoneToCustomerBridge;
use SprykerEco\Yves\Payone\Dependency\Client\PayoneToShipmentBridge;

class PayoneDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_PAYONE = 'payone client';

    public const CLIENT_CUSTOMER = 'customer client';

    public const CLIENT_CART = 'cart client';

    public const CLIENT_SHIPMENT = 'shipment client';

    public const CLIENT_CALCULATION = 'calculation client';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container)
    {
        $container[self::CLIENT_PAYONE] = function (Container $container) {
            return $container->getLocator()->payone()->client();
        };

        $container[self::CLIENT_CUSTOMER] = function (Container $container) {
            return new PayoneToCustomerBridge($container->getLocator()->customer()->client());
        };

        $container[self::CLIENT_CART] = function (Container $container) {
            return new PayoneToCartBridge($container->getLocator()->cart()->client());
        };

        $container[self::CLIENT_SHIPMENT] = function (Container $container) {
            return new PayoneToShipmentBridge($container->getLocator()->shipment()->client());
        };

        $container[self::CLIENT_CALCULATION] = function (Container $container) {
            return new PayoneToCalculationBridge($container->getLocator()->calculation()->client());
        };

        return $container;
    }
}
