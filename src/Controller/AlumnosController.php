<?php

namespace App\Controller;

use App\Entity\Alumnos;
use App\Repository\AlumnosRepository;
use App\Form\AlumnosFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\JsonResponse; <----- para nuestras api rest Symfony
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AlumnosController extends AbstractController
{
    private $entidadAlumno;

    public function __construct(EntityManagerInterface $entidadAlumno) {
        $this->entidadAlumno = $entidadAlumno;
    }

    /** Lista de alumnos */
    #[Route('/alumnos', name: 'alumnos')]
    public function index(ManagerRegistry $doctrineAlumno): Response
    {
        $list_students = 'Alumnos de Salesianos';

        $repository = $doctrineAlumno->getRepository(Alumnos::class);
        $alumnos = $repository->findAll();

        return $this->render('alumnos/lista_alumnos.html.twig', [
            'controller_name' => 'AlumnosController',
            'list_students' => $list_students,
            'alumnos' => $alumnos
        ]);

        // Para devolver datos en el caso de estar realizando una API REST
        /*return $this->json([
            'message' => 'Bienvenido al controlador de alumnos',
            'list_students' => $list_students,
            'alumnos' => $alumnos,
            'path' => 'src/Controller/AlumnosController.php',
        ]);*/
    }

    #[Route('/alumnos/create', name: 'alumno_create')]
    public function create(Request $request, SluggerInterface $slugger, ManagerRegistry $doctrineAlumno): Response
    {
        $titulo_formulario = 'Nuevo alumno';// como cadena de texto

        $alumno = new Alumnos();// para añadir el nuevo dato a través de una entidad

        $formAlumno = $this->createForm(AlumnosFormType::class, $alumno);// creamos nuevo formulario en Symfony
        $formAlumno->handleRequest($request);
        
        if ($formAlumno->isSubmitted()) {// después de enviar los datos

            /* Procedemos a subir la imágen, después de ser seleccionada y dirigirla a la ruta
             que corresponde con nuestra imágen */ 

            $imagen = $formAlumno->get('foto')->getData();// file es el nombre que se le debe dar a la subida por defecto
            //$ruta = str_replace(" ", "-",'..\\images\\'.$alumno->getFoto()); // para reemplazar cualquier archivo

            $nombreEncontrado = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
            $guardarImagen = $slugger->slug($nombreEncontrado);

            $rutaImagen = 'images/'.$guardarImagen.'.'.$imagen->guessExtension();// ponemos la imágen en dicha ruta

            try {
                $imagen->move(
                $this->getParameter('files_directory'),// para fichero de tipo imágen
                    $rutaImagen
                );
            } catch (FileException $ex) {
                throw new \Exception('Problemas al subir la nueva imágen a Salesianos');
            }
            
            $alumno->setFoto($rutaImagen);// para subir la foto a la carpeta images

            $nombre = $alumno->getNombre();

            $this->entidadAlumno->persist($alumno);
            $this->entidadAlumno->flush();

            $this->addFlash('aniadir', 'Alumno '.$nombre.' añadido correctamente');
            return $this->redirectToRoute('alumnos');
        }

        return $this->render('alumnos/aniadir.html.twig', [
            'controller_name' => 'AlumnosController',
            'titulo_formulario' => $titulo_formulario,
            'form' => $formAlumno->createView(),// para añadir el diseño de nuestro formulario
            'alumno' => $formAlumno
        ]);
    }

     /** Para visualizar los datos de un trabajor en forma de lista */
     #[Route('/alumno/datos/{id}', name: 'datos_alumno')]
     public function show(Alumnos $alumno): Response
     {
         $titulo_datos = 'Datos de alumno';
 
         return $this->render('alumnos/datos.html.twig', [
             'controller_name' => 'AlumnosController',
             'titulo_datos' => $titulo_datos,
             'alumno' => $alumno
         ]);
     }
    
      /** Editamos los datos de un trabajador seleccionado */
    #[Route('/alumnos/update/{id}', name: 'alumno_update')]
    public function update(Request $request, Alumnos $alumno,
        AlumnosRepository $alumnosRepository, SluggerInterface $slugger,
        FileSystem $fileImagen): Response
    {
        $titulo_formulario = 'Editar alumno';

        $nombreAnterior = $alumno->getNombre();// nombre del alumno anterior
        $imagenAnterior = $alumno->getFoto();// imágen actual obtenida

        $formAlumno = $this->createForm(AlumnosFormType::class, $alumno);// creamos nuevo formulario en Symfony
        
        $formAlumno->handleRequest($request);

        if ($imagenAnterior) {
            if ($formAlumno->isSubmitted()) {
                $fileImagen->remove($imagenAnterior); // nos permite eliminar una foto de una ruta
                
                $alumnosRepository->save($alumno, true);// aquí se realiza el editar alumno

                $imagen = $formAlumno->get('foto')->getData();// file es el nombre que se le debe dar a la subida por defecto
                // ruta = str_replace(" ", "-",'..\\images\\'.$alumno->getFoto()); //str_replace(): para reemplazar cualquier archivo

                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);

                $guardarNombreImagen = $slugger->slug($originalFilename);// para el nombre de nuestra imágen a guardar
                // Para indicar un identificador extra de imágen: '-'.uniqid().

                $rutaImagen = 'images/'.$guardarNombreImagen.'.'.$imagen->guessExtension();// ponemos la imágen en dicha ruta

                try {
                    $imagen->move(
                    $this->getParameter('files_directory'),// para fichero de tipo imágen
                        $rutaImagen
                    );
                } catch (FileException $e) {
                    throw new \Exception('Problemas al subir la nueva imágen a Salesianos');
                }

                $alumno->setFoto($rutaImagen);// para subir la foto

                //$nombre = $alumno->getNombre();// nombre del alumno editado
                $this->entidadAlumno->persist($alumno);
                $this->entidadAlumno->flush();

                $this->addFlash('editar', 'Alumno, que antes fue '.$nombreAnterior.' editado correctamente');
                return $this->redirectToRoute('alumnos');
            }
        }

        $imagenActual = $alumno->getFoto();

        return $this->render('alumnos/editar.html.twig', [
            'controller_name' => 'AlumnosController',
            'titulo_formulario' => $titulo_formulario,
            'form' => $formAlumno->createView(),
            'alumno' => $alumno,
            'imagenActual' => $imagenActual
        ]);
    }

    /** Eliminamos los datos de un trabajador seleccionado */
    #[Route('/alumnos/delete/{id}', name: 'alumno_delete')]
    public function delete(Alumnos $alumno, AlumnosRepository $alumnosRepository,
        Filesystem $fileImagen): Response
    {
        //$nombreImagen = substr ($alumno->getFoto(), 7);// obtenemos solamente el nombre de imágen porque
        // lo que se elimina es el nombre de la imágen, no la carpeta entera
        $foto = $alumno->getFoto();

        $fileImagen->remove($foto); // nos permite eliminar una foto de una ruta

        /** Procedemos a eliminar la imágen de la fila correspondiente al alumno antes de eliminar al alumno completamente */

        $alumnosRepository->remove($alumno, true);// aquí se realiza el eliminar alumno

        $this->addFlash('eliminar', 'Alumno '.$alumno->getNombre().' eliminado correctamente');
        return $this->redirectToRoute('alumnos');
    }
}
