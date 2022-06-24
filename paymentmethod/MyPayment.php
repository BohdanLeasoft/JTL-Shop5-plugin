<?php

namespace Plugin\myplugin_jtl5\paymentmethod;

require_once __DIR__.'/../vendor/autoload.php';

use JTL\Alert\Alert;
use JTL\Plugin\Payment\Method;
use JTL\Session\Frontend;
use JTL\Mail\Mail\Mail;
use JTL\Mail\Mailer;
use JTL\Shop;
use JTL\Cart\Cart;
use JTL\Checkout\Bestellung;
use Ginger\Ginger;
use Plugin\myplugin_jtl5\builders\CustomerBuilder;
use PHPMailer\PHPMailer\Exception;
use stdClass;
use function Functional\difference;

/**
 * Class MyPayment.
 */
class MyPayment extends Method
{
    /**
     * @var PaymentMethod
     */
    public $paymentMethod = 'credit-card';
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $caption;
    /**
     * @var CustomerBuilder
     */
    public $customerBuilder;

    /**
     * @var Client
     */
    public $client;

    public $endpoint = 'https://endpoint';
    public $apikey  = 'API-KEY';

    public function __construct(string $moduleID)
    {
        $this->customerBuilder = new CustomerBuilder();
        $this->client = Ginger::createClient($this->endpoint, $this->apikey);
        parent::__construct($moduleID);
    }

    /**
     * Sets the name and caption for the payment method
     *
     * @param int $nAgainCheckout
     * @return $this
     */
    public function init(int $nAgainCheckout = 0): self
    {
        $this->name    = 'MyPayment';
        $this->caption = 'MyPayment';

        parent::init($nAgainCheckout);
        return $this;
    }

    /**
     * Check the payment condition for displaying the payment on payment page
     *
     * @param array $args_arr
     * @return bool
     */
    public function isValidIntern(array $args_arr = []): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isValid(object $customer, Cart $cart): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function preparePaymentProcess($order): void
    {
        parent::preparePaymentProcess($order);
        $dataForRequest = [];
        $dataForRequest = $this->getOrderData($order);

        $transaction = $this->client->createOrder($dataForRequest);
        \header('Location: ' . current($transaction['transactions'])['payment_url']);
        exit;
    }

    public function getOrderData($order)
    {
//
//        $orderHash = $this->generateHash($order);
//       // $returnUrl = ($_SESSION['Zahlungsart']->nWaehrendBestellung == 0) ? $this->getNotificationURL($orderHash) : $this->getNotificationURL($orderHash).'&sh=' . $orderHash;
//        $returnUrl = Shop::Container()->getLinkService()->getStaticRoute('bestellabschluss.php');
        return [
            'merchant_order_id' => $order->cBestellNr,
            'currency' => 'EUR',
            'amount' => (int)(($_SESSION['Warenkorb']->gibGesamtsummeWaren(true))*100), // Amount in cents
            'description' => 'Purchase order '.$order->cBestellNr,
            'return_url' => $this->getReturnURL($order),
            'webhook_url' => $this->getReturnURL($order),
            'transactions' => [
                [
                    'payment_method' => $this->paymentMethod
                ]
            ]
        ];
    }
    /**
     * @inheritDoc
     */
    public function handleNotification(Bestellung $order, string $hash, array $args): void
    {
        die("handleNotification");
        // overwrite!
    }

    /**
     * @inheritDoc
     */
    public function finalizeOrder(Bestellung $order, string $hash, array $args): bool
    {
        // overwrite!
        return true;
    }

    /**
     * @inheritDoc
     */
    public function redirectOnCancel(): bool
    {
        // overwrite!
        return true;
    }

    /**
     * @inheritDoc
     */
    public function redirectOnPaymentSuccess(): bool
    {
        die('redirectOnPaymentSuccess');
        // overwrite!
        return false;
    }
}
