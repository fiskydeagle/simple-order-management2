<?php


namespace App\Services;

use App\Entity\Order;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class PdfGenerator
{
    private $templating;
    private $appKernel;
    private $request;

    public function __construct(\Twig\Environment $templating, KernelInterface $appKernel, RequestStack $request)
    {
        $this->templating = $templating;
        $this->appKernel = $appKernel;
        $this->request = $request;
    }

    public function generateDeliveryNotes(Order $order, $save = null) {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->templating->render('pdf/notes.html.twig', [
            'order' => $order
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        if (!$save) {
            // Output the generated PDF to Browser (inline view)
            return $dompdf->stream("deliveryNotes.pdf", [
                "Attachment" => false
            ]);
        }

        // Store PDF Binary Data
        $output = $dompdf->output();

        $fileName = '/public/'. md5(uniqid()) .'.pdf';
        $publicDirectory = $this->appKernel->getProjectDir();
        $pdfFilepath =  $publicDirectory . '/uploads' . $fileName;

        file_put_contents($pdfFilepath, $output);

        return new JsonResponse(array(
            'pdf' => 'http://localhost' . $fileName
        ));
    }

    public function generateBill(Order $order, $save = null) {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->templating->render('pdf/bill.html.twig', [
            'order' => $order
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        if (!$save) {
            // Output the generated PDF to Browser (inline view)
            return $dompdf->stream("bill.pdf", [
                "Attachment" => false
            ]);
        }

        // Store PDF Binary Data
        $output = $dompdf->output();

        $fileName = '/uploads/'. md5(uniqid()) .'.pdf';
        $publicDirectory = $this->appKernel->getProjectDir();
        $pdfFilepath =  $publicDirectory . '/public' . $fileName;

        file_put_contents($pdfFilepath, $output);

        return new JsonResponse(array(
            'pdf' => 'http://localhost' . $fileName
        ));
    }
}