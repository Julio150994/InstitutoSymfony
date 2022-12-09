<?php

namespace App\Controller;

use App\Entity\Alumnos;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// Ruta inicial para nuestras solicitudes HTTP en Symfony 6

/**
 * @Route("/api", name="api_")
 */

class AlumnosAPIController extends AbstractController
{
    /** Para mostrar los alumnos en API REST */
    
    /**
    * @Route("/alumnos", name="alumnos_get", methods={"GET"})
    */
    
    #[Route('/api/alumnos', name: 'alumnos_get')]
    public function index(ManagerRegistry $doctrineAlumnos): Response
    {
        $alumnos = $doctrineAlumnos->getRepository(Alumnos::class)
        ->findAll();

        $dataAlumnos = [];

        foreach ($alumnos as $alumno) {
            $dataAlumnos[] = [
                'id' => $alumno->getId(),
                'nombre' => $alumno->getNombre(),
                'apellidos' => $alumno->getApellidos(),
                'edad' => $alumno->getEdad(),
                'foto' => $alumno->getFoto(),
                'preciomatricula' => $alumno->getPreciomatricula()
            ];
        }    
            
        return $this->json($dataAlumnos);// para devolver los datos en formato JSON
    }

    /** Para añadir nuevo alumno a la base de datos */

    /**
     * @Route("/alumno", name="alumno_create", methods={"POST"})
     */

    #[Route('/alumno', name: 'alumno_create')]
    public function create(ManagerRegistry $doctrineAlumnos, Request $request): Response
    {
        $entidadAlumno = $doctrineAlumnos->getManager();

        $alumno = new Alumnos();
        $alumno->setNombre($request->get('nombre'));
        $alumno->setApellidos($request->get('apellidos'));
        $alumno->setEdad($request->get('edad'));
        $alumno->setFoto($request->get('foto'));// ver posteriormente como ver fotos
        $alumno->setPreciomatricula($request->get('preciomatricula'));

        $entidadAlumno->persist($alumno);
        $entidadAlumno->flush();

        return $this->json('Created new project successfully with id ' . $alumno->getId());
    }


    /** Para visualizar los datos de un alumno seleccionado */

    /**
     * @Route("/alumno/{id}", name="alumno_show", methods={"GET"})
     */

    #[Route('/alumno/{id}', name: 'alumno_show')]
    public function show(ManagerRegistry $doctrineAlumnos, int $id): Response
    {
        $alumno = $doctrineAlumnos->getRepository(Alumnos::class)->find($id);

        if (!$alumno) {// el alumno seleccionado no existe
            return $this->json('Alumno de Salesianos no encontrado por id'. $id, 404);
        }

        $dataAlumnos =  [
            'id' => $alumno->getId(),
            'nombre' => $alumno->getNombre(),
            'apellidos' => $alumno->getApellidos(),
            'edad' => $alumno->getEdad(),
            'foto' => $alumno->getFoto(),
            'preciomatricula' => $alumno->getPreciomatricula()
        ];

        return $this->json($dataAlumnos);
    }

    /**
     * @Route("/alumno/{id}", name="alumno_edit", methods={"PUT"})
     */

    #[Route('/alumno/{id}', name: 'alumno_edit')]
    public function edit(ManagerRegistry $doctrineAlumnos, Request $request, int $id): Response
    {
        $entidadAlumno = $doctrineAlumnos->getManager();
        $alumno = $entidadAlumno->getRepository(Alumnos::class)->find($id);
        
        if (!$alumno) {
            return $this->json('Alumno de Salesianos no encontrado por id' . $id, 404);
        }

        $alumno->setNombre($request->get('nombre'));
        $alumno->setApellidos($request->get('apellidos'));
        $alumno->setEdad($request->get('edad'));
        $alumno->setFoto($request->get('foto'));// ver como editar imágen

        $entidadAlumno->flush();

        $dataAlumnos =  [
            'id' => $alumno->getId(),
            'nombre' => $alumno->getNombre(),
            'apellidos' => $alumno->getApellidos(),
            'edad' => $alumno->getEdad(),
        ];

        return $this->json($dataAlumnos);
    }

    /**
     * @Route("/alumno/{id}", name="alumno_delete", methods={"DELETE"})
     */

    #[Route('/alumno/{id}', name: 'alumno_delete')]
    public function delete(ManagerRegistry $doctrineAlumnos, int $id): Response
    {
        $entidadAlumno = $doctrineAlumnos->getManager();
        $alumno = $entidadAlumno->getRepository(Alumnos::class)->find($id);

        if (!$alumno) {
            return $this->json('Alumno de Salesianos no encontrado por id' . $id, 404);
        }

        $entidadAlumno->remove($alumno);
        $entidadAlumno->flush();

        return $this->json('Alumno eliminado correctamente con id ' . $id);
    }
}
