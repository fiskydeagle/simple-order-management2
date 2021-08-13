<?php
namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final class CustomerPaymentOverdueDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Customer::class === $resourceClass && $operationName == 'get-payment-overdue';
    }
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        return $this->customerRepository->findOverduePayments();
    }
}