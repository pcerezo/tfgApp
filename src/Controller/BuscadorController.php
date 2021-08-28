<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\SubirArchivoMedicionType;
use App\Form\EdicionMedicionType;
use App\Form\BotonEdicionMedicionType;
use App\Form\BotonBorrarMedicionType;
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

        $directorioMedicion = $this->getParameter('directorio_mediciones')."/".$info["grafico"]."_".$info["fecha"];

        $nombreZip = "$directorioMedicion/descarga.zip";

        if($zip->open($nombreZip, ZipArchive::CREATE) === TRUE) {
            // Añado archivos al zip y lo cierro
            $zip->addFile($directorioMedicion."/".$info['grafico'].".png", $info['grafico'].".png");
            $zip->addFile($directorioMedicion."/".$info['grafico']."_1.png", $info['grafico']."_1.png");
            $zip->addFile($directorioMedicion."/".$info['grafico'].".txt", $info['grafico'].".txt");
            $zip->addFile($directorioMedicion."/www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$info["latitud"]."N".$info["longitud"]."E.html", $info['grafico']."_Meteoblue.html");
            $zip->close();
    
            // Preparo las cabeceras para descargar el zip
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Cache-Control: no-cache, must-revalidate");
            header("Expires: 0");
            header('Content-Disposition: attachment; filename="'.basename($nombreZip).'"');
            header('Content-Length: ' . filesize($nombreZip));
            header('Pragma: public');

            // Se descarga
            readfile("$directorioMedicion/descarga.zip");

            echo 'ok';
        }
        else echo 'failed';
    }

    #[Route('/busqueda/detalles', name: 'buscador_detalles')]
    public function detalles($id, Request $request): Response {
        $logueado = $this->getUser();
        $editarDatos = false;

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
        // Obtengo el texto del fichero de las observaciones
        $directorioMedicion = $this->getParameter('directorio_mediciones')."/".$info["grafico"]."_".$info["fecha"];
        $archivo_observaciones = $directorioMedicion."/".$info["grafico"]."_observaciones.txt";

        // El archivo si existe debe contener algo
        if (file_exists($archivo_observaciones) && filesize($archivo_observaciones) > 0){
            $descriptor_observaciones = fopen($archivo_observaciones, "r");
            $texto_observaciones = fread($descriptor_observaciones, filesize($archivo_observaciones));
        }
        else {
            // Si no existe dejamos la variable vacía
            $texto_observaciones = "";
        }

        // Obtengo los gráficos generados
        $grafico = "../../uploads/mediciones/".$info["grafico"]."_".$info["fecha"]."/".$info["grafico"].".png";
        $grafico_1 = "../../uploads/mediciones/".$info["grafico"]."_".$info["fecha"]."/".$info["grafico"]."_1.png";
        
        // Obtengo la tabla de datos del fichero de Meteoblue
        $enlaceMeteo = "https://www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$info["latitud"]."N".$info["longitud"]."E";

        // Obtengo el texto html de la página de Meteoblue
        $html = file_get_contents($directorioMedicion."/www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$info["latitud"]."N".$info["longitud"]."E.html");

        // Genero el DOM
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html, LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NONET);

        // Ahora accedo a la tabla en la que se muestran los datos meteorológicos
        $xpath = new DomXPath($doc);

        while ($celestialNode = $xpath->query("//td[@class='celestial']")->item(0)) {
            $celestialNode->parentNode->removeChild($celestialNode);
        };

        $nodeList = $xpath->query("//table[@class='table-seeing']");
        $node = $nodeList->item(0);
        //$tabla = $node->ownerDocument->saveXML($node);
        
        // Obtengo la tabla con datos de diversas mediciones
        $tabla = $node->ownerDocument->saveHTML($node);

        // Gestionamos la página según se esté logueado o no
        if ($logueado) {
            // Formulario para la edición de datos
            $infoGenerica = new MedicionGenerica();
            $formularioEdicion = $this->createForm(EdicionMedicionType::class, $infoGenerica);
            $formularioEdicion->handleRequest($request);

            // Botón formulario para cambiar a la edición de datos
            $formularioBotonEdicion = $this->createForm(BotonEdicionMedicionType::class);
            $formularioBotonEdicion->handleRequest($request);

            // Botón formulario para borrar la medición de datos
            $formularioBotonBorrar = $this->createForm(BotonBorrarMedicionType::class);
            $formularioBotonBorrar->handleRequest($request);

            // Si pulsamos el botón para editar lo indicamos
            if ($formularioBotonEdicion->isSubmitted()) {
                $editarDatos = true;
            }
            // Si enviamos los datos editados los guardamos
            else if ($formularioEdicion->isSubmitted()) {
                $fecha = $infoGenerica->getFecha();
                $hora = $infoGenerica->getHora();
                $latitud = $infoGenerica->getLatitud();
                $longitud = $infoGenerica->getLongitud();
                $altitud = $infoGenerica->getAltitud();
                $tempInfrarroja = $infoGenerica->getTempInfrarroja();
                $tempSensor = $infoGenerica->getTempSensor();
                $observaciones = $infoGenerica->getObservaciones();

                // Obtenemos el objeto de la base de datos a partir de su id
                $medicionGenerica = $entityManager->getRepository(MedicionGenerica::class)->find($id);
                // Asignamos los nuevos datos
                if ($fecha != null) {
                    $medicionGenerica->setFecha($fecha);
                    //Hay que cambiar el nombre de la carpeta
                    rename($directorioMedicion, $this->getParameter('directorio_mediciones')."/".$info["grafico"]."_".$fecha->format('Y-m-d'));
                    $directorioMedicion = $this->getParameter('directorio_mediciones')."/".$info["grafico"]."_".$fecha->format('Y-m-d');
                }
                if ($hora != null) {
                    $medicionGenerica->setHora($hora);
                }
                if ($latitud != null) {
                    $medicionGenerica->setLatitud($latitud);
                }
                if ($longitud != null) {
                    $medicionGenerica->setLongitud($longitud);
                }
                if ($altitud != null) {
                    $medicionGenerica->setAltitud($altitud);
                }
                if ($tempInfrarroja != null) {
                    $medicionGenerica->setTempInfrarroja($tempInfrarroja);
                }
                if ($tempSensor != null) {
                    $medicionGenerica->setTempSensor($tempSensor);
                }
                if ($observaciones != null || $observaciones != "") {
                    $descriptor_observaciones = fopen($directorioMedicion."/".$info['grafico']."_observaciones.txt", "w");
                    fwrite($descriptor_observaciones, $observaciones);
                    fclose($descriptor_observaciones);
                }

                $entityManager->flush();

                return $this->redirectToRoute('buscador_mediciones');
            }
            // Si se pulsa el botón de borrar, se elimina la medición
            else if($formularioBotonBorrar->isSubmitted()) {
                $medicionGenerica = $entityManager->getRepository(MedicionGenerica::class)->find($id);

                $entityManager->remove($medicionGenerica);
                $entityManager->flush();

                return $this->redirectToRoute('buscador_mediciones');
                /*$conn = $entityManager->getConnection();
                
                $sql = "DELETE FROM medicion_individual WHERE generica_id=$id";

                // Se ejecuta la sentencia SQL
                $sentencia = $conn->prepare($sql);
                $sentencia->execute();*/
            }

            return $this->render('buscador/detalles.html.twig', [
                'logueado' => $logueado,
                'info' => $info,
                'grafico' => $grafico,
                'grafico_1' => $grafico_1,
                'enlace' => $enlaceMeteo,
                'tabla' => $tabla,
                'observaciones' => $texto_observaciones,
                'editarDatos' => $editarDatos,
                'formBotonBorrar' => $formularioBotonBorrar->createView(),
                'formBotonEditar' => $formularioBotonEdicion->createView(),
                'formEdicionMedicion' => $formularioEdicion->createView(),
            ]);
        }

        return $this->render('buscador/detalles.html.twig', [
            'logueado' => $logueado,
            'info' => $info,
            'grafico' => $grafico,
            'grafico_1' => $grafico_1,
            'enlace' => $enlaceMeteo,
            'tabla' => $tabla,
            'observaciones' => $texto_observaciones,
            'editarDatos' => $editarDatos,
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

        // Obtengo el último id para obtener el siguiente
        /*$sql_lastId = 'SELECT Id FROM medicion_generica ORDER BY medicion_generica.id DESC LIMIT 1';
        $sentencia = $conn->prepare($sql_lastId);
        $sentencia->execute();
        $lastId = $sentencia->fetch();

        $nextId = $lastId["Id"]+1;*/
        

        // Sólo los usuarios logueados pueden acceder al formulario
        // por el que se suben los archivos de medición
        if ($logueado) {
            $formArchivo = $this->createForm(SubirArchivoMedicionType::class, $archivo);
            $formArchivo->handleRequest($request);

            // Si pulsamos aceptar subimos el archivo
            if ($formArchivo->isSubmitted() && $formArchivo->isValid()) {
                // Obtengo el archivo de datos
                $file = $archivo->getNombre();
                // Se le da nombre y extensión
                $nombre = $file->getClientOriginalName();
                $lugar = $archivo->getLugar();
                $extension = pathinfo($nombre, PATHINFO_EXTENSION);

                // Damos un nombre base sin espacios para los ficheros que se crean
                $salida = str_replace(' ', '', $archivo->getLugar());
                $filename = $salida.".".$extension;
                
                // El archivo debe ser .txt y no estar vacío
                if ($extension == "txt") {         
                    
                    // Se obtienen los datos del archivo que se sube
                    $media_temp_sensor = $media_temp_infrarroja = $media_sl = $media_bat = 0;

                    // Lectura del archivo de medición para almacenar sus datos en la base de datos
                    $medicion = file($file);
                    
                    // Se comprueba que el archivo de texto sigue el formato de datos
                    // # 	TASD00	ci:20.48	T IR	T Sens	Mag 	Hz 	Alt	Azi 	Lat 	Lon 	SL	Bat
                    $indices = explode("\t", $medicion[0]);
                    
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
                    
                    // Obtenemos los valores de las medias
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
                    $medicionGenerica->setArchivo($nombre);
                    $medicionGenerica->setLocalizacion($lugar);
                    $medicionGenerica->setGrafico($salida);
                    $medicionGenerica->setAutoria($this->getUser()->getUsername());

                    // Valores media
                    $medicionGenerica->setTempSensor($temp_sensor);
                    $medicionGenerica->setTempInfrarroja($temp_infrarroja);
                    $medicionGenerica->setAltitud($sl);
                    $medicionGenerica->setBat($bat);

                    // Se almacena en la base de datos
                    $entityManager->persist($medicionGenerica);
                    $entityManager->flush();

                    // Creada la medición genérica volvemos a leer quedándonos con los datos individuales
                    $medicion = file($file);
                
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

                    // Se sube el archivo a su directorio <lugar>_<fecha>
                    $directorioMediciones = $this->getParameter('directorio_mediciones')."/".$salida."_".$dateString;
                    if (!file_exists($directorioMediciones)) {
                        mkdir($directorioMediciones);
                    }
                    // Se sube el archivo de las mediciones a su directorio
                    $file->move($directorioMediciones, $filename);

                    // Se anotan las observaciones escritas en el formulario
                    // en un archivo con permisos de escritura
                    $observaciones = $directorioMediciones."/".$salida."_observaciones.txt";
                    $descriptorObservaciones = fopen($observaciones, "w");
                    fwrite($descriptorObservaciones, $archivo->getObservaciones());
                    fclose($descriptorObservaciones);

                    // SCRIPT interpolador.py
                    // Se crea el gráfico por defecto
                    $command = escapeshellcmd("python3 /home/pabloc/Documentos/GII/TFG/tfgApp/interpolador.py ".$directorioMediciones."/".$filename." ".$directorioMediciones."/".$salida.".png");
                    // Se crea el gráfico suavizado
                    $command_1 = escapeshellcmd("python3 /home/pabloc/Documentos/GII/TFG/tfgApp/interpolador.py ".$directorioMediciones."/".$filename." ".$directorioMediciones."/".$salida."_1.png 1");
                    // Se ejecuta el script
                    shell_exec($command);
                    shell_exec($command_1);

                    // WGET Página de Meteoblue para las coordenadas
                    $enlaceMeteo = "https://www.meteoblue.com/es/tiempo/outdoorsports/seeing/".$latitud."N".$longitud."E";
                    $obtenerMeteo = escapeshellcmd("wget -p -k -E $enlaceMeteo -P $directorioMediciones");
                    shell_exec($obtenerMeteo);

                    $subido = 'true';
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

}
