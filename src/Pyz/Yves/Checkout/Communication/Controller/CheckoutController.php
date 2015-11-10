<?php

namespace Pyz\Yves\Checkout\Communication\Controller;

use Generated\Shared\Transfer\CartTransfer;
use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutRequestTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerErrorTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ShipmentMethodAvailabilityTransfer;
use Pyz\Yves\Checkout\Communication\Form\CheckoutType;
use Pyz\Yves\Checkout\Communication\Plugin\CheckoutControllerProvider;
use SprykerEngine\Yves\Application\Communication\Controller\AbstractController;
use Pyz\Yves\Checkout\Communication\CheckoutDependencyContainer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method CheckoutDependencyContainer getDependencyContainer()
 */
class CheckoutController extends AbstractController
{

    /**
     * @return CartTransfer
     */
    public function getCart()
    {
        return $this->getLocator()->cart()->client()->getCart();
    }

    /**
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $container = $this->getDependencyContainer();
        $shipmentMethodAvailabilityTransfer = new ShipmentMethodAvailabilityTransfer();
        $shipmentMethodAvailabilityTransfer->setCart($this->getCart());

        $shipmentTransfer = $container->createShipmentClient()
            ->getAvailableMethods($shipmentMethodAvailabilityTransfer);

        $checkoutForm = $container->createCheckoutForm($request, $shipmentTransfer);

        $checkoutTransfer = new CheckoutRequestTransfer();

        $checkoutForm = $this->createForm($checkoutForm, $checkoutTransfer);

        if ($request->isMethod('POST')) {
            if ($checkoutForm->isValid()) {
                $checkoutClient = $this->getLocator()->checkout()->client();
                /** @var CheckoutRequestTransfer $checkoutRequest */
                $checkoutRequest = $checkoutForm->getData();

                foreach ($shipmentTransfer->getMethods() as $shipmentMethod) {
                    if ($shipmentMethod->getIdShipmentMethod() === $checkoutRequest->getIdShipmentMethod()) {
                        $checkoutRequest->setShipmentMethod($shipmentMethod);
                    }
                }

                $checkoutRequest->setCart($this->getCart());
                $checkoutRequest->setShippingAddress($checkoutRequest->getBillingAddress());

                $createAccount = $checkoutForm[CheckoutType::FIELD_CREATE_ACCOUNT]->getData();
                if ($createAccount) {
                    $checkoutRequest->setCustomerPassword($checkoutForm[CheckoutType::FIELD_PASSWORD]->getData());
                }

                /** @var CheckoutResponseTransfer $checkoutResponseTransfer */
                $checkoutResponseTransfer = $checkoutClient->requestCheckout($checkoutRequest);

                if ($checkoutResponseTransfer->getIsSuccess()) {
                    $this->getLocator()->cart()->client()->clearCart();

                    return $this->redirect($checkoutResponseTransfer);
                } else {
                    return $this->errors($checkoutResponseTransfer->getErrors());
                }
            }
        }

        return [
            'form' => $checkoutForm->createView(),
            'cart' => $this->getCart(),
        ];
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function successAction(Request $request)
    {
        //@todo copy look and feel from invision!
        //@todo add finish form?

        return [];
    }

    /**
     * @param CheckoutErrorTransfer[] $errors
     *
     * @return JsonResponse
     */
    protected function errors($errors)
    {
        $returnErrors = [];
        foreach ($errors as $error) {
            $returnErrors[] = [
                'errorCode' => $error->getErrorCode(),
                'message' => $error->getMessage(),
                'step' => $error->getStep(),
            ];
        }

        return new JsonResponse([
            'success' => false,
            'errors' => $returnErrors,
        ]);
    }

    /**
     * @param CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return JsonResponse
     */
    public function redirect(CheckoutResponseTransfer $checkoutResponseTransfer)
    {
        $redirectUrl = $checkoutResponseTransfer->getIsExternalRedirect()
            ? $checkoutResponseTransfer->getRedirectUrl()
            : CheckoutControllerProvider::ROUTE_CHECKOUT_SUCCESS;

        return new JsonResponse([
            'success' => true,
            'url' => $redirectUrl,
        ]);
    }

}
