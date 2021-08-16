<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SubirArchivoMedicionType;
use App\Form\User;
use ZipArchive;
use DOMDocument;
use DomXPath;
use App\Entity\ArchivoMedicion;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use DateTime;

use App\Entity\MedicionGenerica;
use App\Entity\MedicionIndividual;

class BuscadorController extends AbstractController
{
    #[Route('/busqueda', name: 'buscador')]
    public function index(): Response
    {
        $logueado = $this->getUser();
    
        return $this->redirectToRoute('buscador_mediciones');
    }

    #[Route('/busqueda/descargar', name: 'buscador_descargar')]
    public function descargarTodo($id): Response {
        $logueado = $this->getUser();
        // Inicio el objeto que crea el comprimido zip
        $zip = new ZipArchive();

        // A partir del Id obtengo el objeto de medición en cuestión
        $entityManager = $this->getDoctrine()->getManager();

        // Obtener el nombre del archivo a partir del id
        $conn = $entityManager->getConnection();

        $sql = "SELECT * FROM medicion_generica WHERE id=$id";

        // Se ejecuta la sentencia SQL
        $sentencia = $conn->prepare($sql);
        $sentencia->execute();
        $datos = $sentencia->fetchAll();
        $info = $datos[0];

        $directorioMedicion = $this->getParameter('directorio_mediciones')."/".$info["grafico"];

        $nombreZip = "$directorioMedicion/descarga.zip";

        if($zip->open($nombreZip, ZipArchive::CREATE) === TRUE) {
            // Añado archivos al zip y lo cierro
            $zip->addFile($directorioMedicion."/".$info['grafico'].".png", $info['grafico'].".png");
            $zip->addFile($directorioMedicion."/".$info['grafico'].".png", $info['grafico'].".txt");
            $zip->close();
    
            // Descargo el zip
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: 0");
            header('Content-Disposition: attachment; filename="'.basename($nombreZip).'"');
            header('Content-Length: ' . filesize($nombreZip));
            header('Pragma: public');
            readfile("$directorioMedicion/descarga.zip");

            echo 'ok';
        }
        else echo 'failed';

        return $this->render("buscador/datos.html.twig", [
            'logueado' => $logueado,
        ]);
    }

    #[Route('/busqueda/detalles', name: 'buscador_detalles')]
    public function detalles($id): Response {
        $logueado = $this->getUser();

        // A partir del Id obtengo el objeto de medición en cuestión
        $entityManager = $this->getDoctrine()->getManager();

        // Mostrar los datos disponibles en forma de galería
        $conn = $entityManager->getConnection();

        $sql = "SELECT * FROM medicion_generica WHERE id=$id";

        // Se ejecuta la sentencia SQL
        $sentencia = $conn->prepare($sql);
        $sentencia->execute();
        $datos = $sentencia->fetchAll();

        // Sólo hay una fila, así que cogemos la información directamente
        $info = $datos[0];
        $grafico = "../../uploads/mediciones/".$info["grafico"]."/".$info["grafico"].".png";

        $enlaceMeteo = "https://www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$info["latitud"]."N".$info["longitud"]."E";

        // Obtengo el texto html de la página de Meteoblue
        $html = file_get_contents($this->getParameter('directorio_mediciones')."/".$info["grafico"]."/www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$info["latitud"]."N".$info["longitud"]."E.html");

        // Genero el DOM
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html, LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NONET);

        // Ahora accedo a la tabla en la que se muestran los datos meteorológicos
        $xpath = new DomXPath($doc);
        $nodeList = $xpath->query("//table[@class='table-seeing']");
        $node = $nodeList->item(0);
        //$tabla = $node->ownerDocument->saveXML($node);
        
        // Obtengo la tabla con datos de diversas mediciones
        $tabla = $node->ownerDocument->saveHTML($node);

        return $this->render('buscador/detalles.html.twig', [
            'logueado' => $logueado,
            'info' => $info,
            'grafico' => $grafico,
            'enlace' => $enlaceMeteo,
            'tabla' => $tabla,
        ]);
    }

    #[Route('/busqueda/mediciones_fotos', name: 'buscador_mediciones')]
    public function mediciones_fotos(Request $request): Response{
        $logueado = $this->getUser();
        $archivo = new ArchivoMedicion();

        // Obtengo el manejador de la base de datos
        $entityManager = $this->getDoctrine()->getManager();

        // Mostrar los datos disponibles en forma de galería
        $conn = $entityManager->getConnection();

        $sql = 'SELECT * FROM medicion_generica ORDER BY medicion_generica.fecha';

        $sentencia = $conn->prepare($sql);
        $sentencia->execute();

        $datos = $sentencia->fetchAll();

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
                    // Si el formato es .txt subimos el archivo
                    $directorioMediciones = $this->getParameter('directorio_mediciones')."/".$archivo->getLugar()."_".$idHasheado;
                    if (!file_exists($directorioMediciones)) {
                        mkdir($directorioMediciones);
                    }
                    $file->move($directorioMediciones, $filename);
                    $subido = 'true';

                    // se crea la imagen de interpolación
                    $salida = $archivo->getLugar()."_".$idHasheado;
                    $command = escapeshellcmd("python3 /home/pabloc/Documentos/GII/TFG/tfgApp/interpolador.py ".$directorioMediciones."/".$filename." ".$directorioMediciones."/".$salida.".png 1");
                    shell_exec($command);

                    $media_temp_sensor = $media_temp_infrarroja = $media_sl = $media_bat = 0;

                    // Lectura del archivo de medición para almacenar sus datos en la base de datos
                    $medicion = file($directorioMediciones."/".$filename);
                    // Se salta a la segunda línea que es medicion[1]

                    // Datos genéricos TODO: alterar la BD según explicado en el diseño en la documentación
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
                    

                    $num_mediciones = $registros[0];

                    $media_temp_sensor = $media_temp_sensor/$num_mediciones;
                    $media_temp_infrarroja = $media_temp_infrarroja/$num_mediciones;
                    $media_sl = $media_sl/$num_mediciones;
                    $media_bat = $media_bat/$num_mediciones;
                    
                    // Guardamos la info. genérica de la medición
                    $medicionGenerica = new MedicionGenerica();
                    //$date = date_create_from_format('Y-m-d', $fecha);
                    
                    //Pasamos la fecha a string para que se pueda dar formato
                    $date = new DateTime($fecha);
                    $dateString = $date->format('Y-m-d');
                    $dateFormat = date_create_from_format('Y-m-d', $dateString);
                    $time = date_create_from_format('H:i:s', $hora);
                    $medicionGenerica->setFecha($dateFormat);
                    $medicionGenerica->setHora($time);
                    $medicionGenerica->setLatitud($latitud);
                    $medicionGenerica->setLongitud($longitud);
                    $medicionGenerica->setNombre($nombre);
                    $medicionGenerica->setLocalizacion($lugar);
                    $medicionGenerica->setGrafico($salida);
                    $medicionGenerica->setAutoria($this->getUser()->getUsername());

                    // Valores media
                    $medicionGenerica->setTempSensor($temp_sensor);
                    $medicionGenerica->setTempInfrarroja($temp_infrarroja);
                    $medicionGenerica->setAltitud($sl);
                    $medicionGenerica->setMediaMagnitud($magnitud);
                    $medicionGenerica->setBat($bat);

                    // Se almacena en la base de datos
                    $entityManager->persist($medicionGenerica);
                    $entityManager->flush();

                    // Creada la medición genérica volvemos a leer quedándonos con los datos individuales
                    $medicion = file($directorioMediciones."/".$filename);
                   
                    // Datos individuales
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

                    // Página de Meteoblue para las coordenadas
                    $enlaceMeteo = "https://www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$latitud."N".$longitud."E";
                    $obtenerMeteo = escapeshellcmd("wget -p -k -E $enlaceMeteo -P $directorioMediciones");
                    shell_exec($obtenerMeteo);
                }
                else {
                    // En otro caso sólo indicamos el error
                    $subido = 'false';
                }

                if ($subido != 'false') {
                    // Mostramos la página con formulario indicando el éxito de la subida
                    return $this->render('buscador/mediciones_fotos.html.twig', [
                        'controller_name' => 'BuscadorController',
                        'logueado' => $logueado,
                        'form_archivo' => $formArchivo->createView(),
                        'subido' => $subido,
                        'datos' => $datos,
                    ]);
                }
            }

            // Si no pulsamos para subir archivo, mostramos la página con su formulario
            return $this->render('buscador/mediciones_fotos.html.twig', [
                'controller_name' => 'BuscadorController',
                'logueado' => $logueado,
                'form_archivo' => $formArchivo->createView(),
                'subido' => "nada",
                'datos' => $datos,
            ]);
        }

        // Si el usuario no está logueado no hacemos que cargue el formulario
        // para la subida de archivos
        return $this->render('buscador/mediciones_fotos.html.twig', [
            'controller_name' => 'BuscadorController',
            'logueado' => $logueado,
            'datos' => $datos,
        ]);

        
    }

    public function datos(): Response{
        $logueado = $this->getUser();
        return $this->render('buscador/datos.html.twig', [
            'logueado' => $logueado,
        ]);
    }
}
