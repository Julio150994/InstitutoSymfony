<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use App\Entity\Alumnos;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AlumnosPdfController extends AbstractController
{
    #[Route('/alumnos/pdf', name: 'lista_alumnos_pdf')]
    public function index(ManagerRegistry $doctrineAlumno): Response
    {
        $titulo = "Informe de alumnos de Salesianos";

        $repository = $doctrineAlumno->getRepository(Alumnos::class);
        $alumnos = $repository->findAll();

        // Llamamos las variables dentro del array que tenemos para mostrar datos en PDF
        $data = [
            'titulo' => $titulo,
            'alumnos' => $alumnos,
            'foto' => $this->mostrarImagenes('images/logo-pepsi.png'),
        ];

        $html =  $this->renderView('alumnos/pdf/pdf.html.twig', $data);
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        //$dompdf->downloadAlumnosPDF();// aquí descargamo nuestro informe PDF de alumnos 
        $dompdf->render();
    
        return new Response (
            $dompdf->stream('resume', ["Attachment" => false]),
            Response::HTTP_OK,
            [
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'Content-Type' => 'application/pdf',
                //'Content-Disposition'   => 'attachment; filename="alumnos.pdf"'
            ],
        );
    }

    /** Aquí es donde jugamos con las imágenes */
    private function mostrarImagenes($path) {
        return $path;
        /*dd($path);
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;*/
    }

    /** Para descargar nuestro fichero PDF */
    private function downloadAlumnosPDF(){
        $response = new BinaryFileResponse('path/pdf/pdf.pdf');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'alumnos.pdf');
        return $response;
    }
}
