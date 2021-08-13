<?php
namespace App\Controller;

use App\DataProvider\CustomerPaymentOverdueDataProvider;
use App\Entity\Customer;

class CustomerPaymentOverdueAction
{
    private $customerPaymentOverdueDataProvider;


    public function __construct(CustomerPaymentOverdueDataProvider $customerPaymentOverdueDataProvider)
    {
        $this->customerPaymentOverdueDataProvider = $customerPaymentOverdueDataProvider;
    }

    public function __invoke($params = null)
    {
        return $this->customerPaymentOverdueDataProvider->getCollection(Customer::class, 'get-payment-overdue');
    }
}