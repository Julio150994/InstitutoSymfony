<?php

namespace App\Controller;

use App\Entity\Alumnos;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;


class AlumnosExcelController extends AbstractController
{

    /** Exportamos los datos de los alumnos a Excel */
    #[Route('/alumnos/excel', name: 'lista_alumnos_excel')]
    public function exportarExcel(ManagerRegistry $doctrineAlumno): Response {
        $alumnoExcel = new Spreadsheet();

        // Activamos la exportación de Excel
        $spreadAlumno = $alumnoExcel->getActiveSheet();

        $spreadAlumno->setCellValue('A1', "Nombre");
        $spreadAlumno->setCellValue('B1', "Apellidos");
        $spreadAlumno->setCellValue('C1', "Edad");
        $spreadAlumno->setCellValue('D1', "Foto");
        $spreadAlumno->setCellValue('E1', "Precio matrícula");
        
        $repository = $doctrineAlumno->getRepository(Alumnos::class);
        $alumnos = $repository->findAll();

        $sheet = $alumnoExcel->getActiveSheet();
        $sheet->setTitle("Alumnos de Salesianos");

        foreach($alumnos as $alumno){
            $sheet->setCellValue('A2', $alumno->getNombre());
            $sheet->setCellValue('B2', $alumno->getApellidos());
            $sheet->setCellValue('C2', strval($alumno->getEdad()));
            $sheet->setCellValue('D2', $alumno->getFoto());
            $sheet->setCellValue('E2', strval($alumno->getPreciomatricula()));
        }

        // Creamos nuestro fichero Excel (con formato XLSX de Microsoft Office)
        $escribirxlsx = new Xlsx($alumnoExcel);
        //$escribircsv = new Csv($alumnoExcel); <------ para el formato de CSV
        //dd($escribircsv);

        // Creamos un fichero temporal en el sistema
        $nombreFichero = 'alumnos.xlsx';
        $filetemp = tempnam(sys_get_temp_dir(), $nombreFichero);


        // Guardamos el fichero en el fichero Excel
        $escribirxlsx->save($nombreFichero);


        // Devolvemos el excel con nuestros datos almacenados
        return $this->file($filetemp, $nombreFichero, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
