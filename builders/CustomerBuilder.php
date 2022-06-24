<?php

namespace Plugin\myplugin_jtl5\builders;

use JTL\Shop;
use JTL\Session\Frontend;
use JTL\Helpers\Request;
use stdClass;

/**
 * Class CustomerBuilder
 * @package Plugin\myplugin_jtl5\builders
 */
class CustomerBuilder
{
    /**
     * @var customer
     */
    public $customer;

    public function getCustomer()
    {
        $this->customer = Frontend::getCustomer();
        return $this->customer;
    }

    public function getCustomerInfo()
    {
        $this->getCustomer();
        $paymentRequestData = [];

        // Building the customer Data
        $paymentRequestData = [
            'first_name'          => !empty($this->customer->cVorname) ? $this->customer->cVorname : $this->customer->cNachname,
            'last_name'           => !empty($this->customer->cNachname) ? $this->customer->cNachname : $this->customer->cVorname,
            'gender'              => !empty($this->customer->cAnrede) ? $this->customer->cAnrede : null,
            'email_address'       => $this->customer->cMail,
            'customer_ip'         => !empty(Request::getRealIP()) ? Request::getRealIP() : $_SERVER['REMOTE_ADDR'],
            'phone_numbers'       => $this->getPhoneNumbers(),
        ];
        return $paymentRequestData;
    }

    public function getPhoneNumbers()
    {
        $phoneNumbers = [];

        $phoneNumbers[] = !empty($this->customer->cTel) ? $this->customer->cTel : '';
        $phoneNumbers[] = !empty($this->customer->cMobil) ? $this->customer->cMobil : '';

        return $phoneNumbers;
    }

//    public function getAddress(): array
//    {
//        // Extracting the billing address from Frontend Module
//        $billingAddress = Frontend::getCustomer();
//
//        $billingShippingDetails['billing'] = $billingShippingDetails['shipping'] = [
//            'street'       => $billingAddress->cStrasse,
//            'house_no'     => $billingAddress->cHausnummer,
//            'city'         => $billingAddress->cOrt,
//            'zip'          => $billingAddress->cPLZ,
//            'country_code' => $billingAddress->cLand
//        ];
//
//        // Extracting the shipping address from the session object
//        if (!empty($_SESSION['Lieferadresse'])) {
//
//            $shippingAddress = $_SESSION['Lieferadresse'];
//
//            $billingShippingDetails['shipping'] = [
//                'street'       => $shippingAddress->cStrasse,
//                'house_no'     => $shippingAddress->cHausnummer,
//                'city'         => $shippingAddress->cOrt,
//                'zip'          => $shippingAddress->cPLZ,
//                'country_code' => $shippingAddress->cLand
//            ];
//        }
//
//        return $billingShippingDetails;
//    }

}