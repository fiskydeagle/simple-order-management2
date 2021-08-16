<?php
namespace App\Controller;

use App\DataProvider\OrdersSumDataProvider;
use App\Entity\Order;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OrdersSumAction
{
    private $ordersSumDataProvider;
    private $requestStack;


    public function __construct(OrdersSumDataProvider $ordersSumDataProvider, RequestStack $requestStack)
    {
        $this->ordersSumDataProvider = $ordersSumDataProvider;
        $this->requestStack = $requestStack;
    }

    public function __invoke($params = null)
    {
        $startDate = $this->requestStack->getCurrentRequest()->query->get('start_date');
        $endDate = $this->requestStack->getCurrentRequest()->query->get('end_date');

        if (!$this->isDateTime($startDate) || !$this->isDateTime($endDate)) {
            throw new BadRequestHttpException('Please Provide start_date and end_date as Date Format', null, 400);
        }

        if (\DateTime::createFromFormat('Y-m-d', $startDate)->getTimestamp() > \DateTime::createFromFormat('Y-m-d', $endDate)->getTimestamp()) {
            throw new BadRequestHttpException('start_date should not be greater than end_date', null, 400);
        }


        return $this->ordersSumDataProvider->getCollection(Order::class, 'get_orders_sum');
    }

    private function isDateTime($date) {
        return (\DateTime::createFromFormat('Y-m-d', $date) !== false); /* edit */
    }
}