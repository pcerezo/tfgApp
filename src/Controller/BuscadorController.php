<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SubirArchivoMedicionType;
use App\Form\User;
use App\Entity\ArchivoMedicion;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\MedicionGenerica;
use App\Entity\MedicionIndividual;

class BuscadorController extends AbstractController
{
    #[Route('/busqueda', name: 'buscador')]
    public function index(): Response
    {
        $logueado = $this->getUser();
    
        return $this->redirectToRoute('buscador_mapas');
    }

    #[Route('/busqueda/mapas_fotos', name: 'buscador_mapas')]
    public function mapas_fotos(Request $request): Response{
        $logueado = $this->getUser();
        $archivo = new ArchivoMedicion();

        // Sólo los usuarios logueados pueden acceder al formulario
        // por el que se suben los archivos de medición
        if ($logueado) {
            $formArchivo = $this->createForm(SubirArchivoMedicionType::class, $archivo);

            $formArchivo->handleRequest($request);
            // Si pulsamos aceptar subimos el archivo
            if ($formArchivo->isSubmitted() && $formArchivo->isValid()) {
                //Obtengo el archivo
                $file = $archivo->getNombre();
                $nombre = $file->getClientOriginalName();
                $lugar = $archivo->getLugar();
                $extension = pathinfo($nombre, PATHINFO_EXTENSION);
                $idHasheado = md5($archivo->getId());
                $filename = $archivo->getLugar()."_".$idHasheado.".".$extension;

                if ($extension == "txt") {
                    // Obtengo el manejador de la base de datos
                    $entityManager = $this->getDoctrine()->getManager();

                    // Si el formato es .txt subimos el archivo
                    $file->move($this->getParameter('directorio_mediciones'), $filename);
                    $subido = 'true';

                    $media_temp_sensor = $media_temp_infrarroja = $media_sl = $media_bat = 0;

                    // Lectura del archivo de medición para almacenar sus datos en la base de datos
                    $medicion = file("../public/uploads/mediciones/".$filename);
                    // Se salta la primera línea
                    //$linea = fgets($medicion[1]);
                    //$linea = $medicion[1];

                    foreach($medicion as $linea) {
                        //Se lee cada dato separado por tabulación
                        $linea = $medicion[1];
                        $registros = explode("\t", $linea);
                        $fecha = $registros[1];
                        $hora = $registros[2];
                        $temp_infrarroja = $registros[3];
                        $temp_sensor = $registros[4];
                        $magnitud = $registros[5];
                        $latitud = $registros[9];
                        $longitud = $registros[10];
                        $sl = $registros[11];
                        $bat = $registros[12];

                        $media_temp_sensor += $temp_sensor;
                        $media_temp_infrarroja += $temp_infrarroja;
                        $media_sl += $sl;
                        $media_bat += $bat;
                    }
                    //} while (!feof($medicion));

                    $num_mediciones = $registros[0];

                    $media_temp_sensor = $media_temp_sensor/$num_mediciones;
                    $media_temp_infrarroja = $media_temp_infrarroja/$num_mediciones;
                    $media_sl = $media_sl/$num_mediciones;
                    $media_bat = $media_bat/$num_mediciones;
                    
                    // Guardamos la info. genérica de la medición
                    $medicionGenerica = new MedicionGenerica();
                    $date = date_create_from_format('Y-m-d', $fecha);
                    $time = date_create_from_format('H:i:s', $hora);
                    $medicionGenerica->setFecha($date);
                    $medicionGenerica->setHora($time);
                    $medicionGenerica->setLatitud($latitud);
                    $medicionGenerica->setLongitud($longitud);
                    $medicionGenerica->setNombre($nombre);
                    $medicionGenerica->setLocalizacion($lugar);
                    // Valores media
                    $medicionGenerica->setTempSensor($temp_sensor);
                    $medicionGenerica->setTempInfrarroja($temp_infrarroja);
                    $medicionGenerica->setAltitud($sl);
                    $medicionGenerica->setMediaMagnitud($magnitud);
                    $medicionGenerica->setBat($bat);
                    // Se almacena en la base de datos
                    $entityManager->persist($medicionGenerica);
                    $entityManager->flush();

                    //fclose($medicion);

                    // Creada la medición genérica volvemos a leer quedándonos con los datos individuales
                    $medicion = file("../public/uploads/mediciones/".$filename);//, "r");
                    // Se salta la primera línea
                    //$linea = fgets($medicion);

                    foreach($medicion as $linea) {
                        //Se lee cada dato separado por tabulación
                        //$linea = fgets($medicion);
                        $registros = explode("\t", $linea);

                        $magnitud = $registros[5];
                        $hz = $registros[6];
                        $declinacion = $registros[7];
                        $azimut = $registros[8];

                        $medicionIndividual = new MedicionIndividual();
                        $medicionIndividual->setGenerica($medicionGenerica);
                        $medicionIndividual->setDeclinacion((float)$declinacion);
                        $medicionIndividual->setAzimut((float)$azimut);
                        $medicionIndividual->setMagnitud((float)$magnitud);
                        // Se inserta en la base de datos
                        $entityManager->persist($medicionIndividual);
                        $entityManager->flush();
                    }
                    //} while(!feof($medicion));

                    //fclose($medicion);
                }
                else {
                    // En otro caso sólo indicamos el error
                    $subido = 'false';
                }

                return $this->render('buscador/mapas_fotos.html.twig', [
                    'controller_name' => 'BuscadorController',
                    'logueado' => $logueado,
                    'form_archivo' => $formArchivo->createView(),
                    'subido' => $subido,
                ]);
            }

            return $this->render('buscador/mapas_fotos.html.twig', [
                'controller_name' => 'BuscadorController',
                'logueado' => $logueado,
                'form_archivo' => $formArchivo->createView(),
                'subido' => "nada",
            ]);
        }

        // Si el usuario no está logueado no hacemos que cargue el formulario
        // para la subida de archivos
        return $this->render('buscador/mapas_fotos.html.twig', [
            'controller_name' => 'BuscadorController',
            'logueado' => $logueado,
        ]);

        
    }

    public function datos(): Response{
        $logueado = $this->getUser();
        return $this->render('buscador/datos.html.twig', [
            'logueado' => $logueado,
        ]);
    }
}
