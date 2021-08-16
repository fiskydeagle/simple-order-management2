<?php
namespace App\Controller;

use App\Entity\Order;
use App\Services\PdfGenerator;

class DeliveryNotesAction
{
    private $pdfGenerator;

    public function __construct(PdfGenerator $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    public function __invoke(Order $data)
    {
        return $this->pdfGenerator->generateDeliveryNotes($data, true);
    }
}