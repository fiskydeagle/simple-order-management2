<?php
namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class OrdersSumDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{

    private $requestStack;
    private $orderRepository;

    public function __construct(RequestStack $requestStack, OrderRepository $orderRepository)
    {
        $this->requestStack = $requestStack;
        $this->orderRepository = $orderRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return Order::class === $resourceClass && $operationName == 'get_orders_sum';
    }
    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $startDate = $this->requestStack->getCurrentRequest()->query->get('start_date');
        $endDate = $this->requestStack->getCurrentRequest()->query->get('end_date');

        if (!$this->isDateTime($startDate) || !$this->isDateTime($endDate)) {
            return [];
        }

        return $this->orderRepository->findOrdersSumByRangeDates($startDate . ' 00:00', $endDate . ' 23:59');
    }

    private function isDateTime($date) {
        return (\DateTime::createFromFormat('Y-m-d', $date) !== false); /* edit */
    }
}