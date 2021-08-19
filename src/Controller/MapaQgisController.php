<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapaQgisController extends AbstractController
{
    #[Route('/mapa', name: 'mapa_qgis')]
    public function index(): Response
    {
        // Valores por defecto
        $activeInicio = $activeBusqueda = $activeContacto = $activeLogin = "";
        $logueado = false;
        $nick = $nombrecompleto = $role = $fotoPerfil = $bio = "";

        // Damos valores concretos si el usuario está logueado
        if ($this->getUser()) {
            $logueado = true;
            $nick = $this->getUser()->getNick();
            $nombrecompleto = $this->getUser()->getNombreCompleto();
            $role = $this->getUser()->getRoles();
            $fotoPerfil = $this->getUser()->getFotoPerfil();
        }

        // Lectura de la biografía del usuario contenida en un archivo
        $archivo_bio = fopen("../public/uploads/bios_perfil/prueba.txt", "r");
        while (!feof($archivo_bio)) {
            $linea = fgets($archivo_bio);
            $bio = $bio.$linea; // Se concatena línea a línea
        }

        // Doy valores a los datos para el mapa
        $rutaDatosMapas = $this->getParameter('directorio_mapa_js')."/medicionesCielo_5.js";

        // Accedemos a la base de datos para obtener las mediciones
        $entityManager = $this->getDoctrine()->getManager();
        $conn = $entityManager->getConnection();

        $sql_datos = "SELECT * FROM medicion_generica";
        $sql_filas = "SELECT COUNT(*) FROM medicion_generica";

        // Se ejecuta la sentencia SQL
        $sentencia = $conn->prepare($sql_datos);
        $sentencia->execute();

        // Escribo los datos js en el fichero de datos para el mapa
        $contenido = 'var json_medicionesCielo_5 = ';
        
        $datos_js = array('type' => 'FeatureCollection',
            'name' => 'medicionesCielo_5',
            'crs' => array('type' => 'name', 'properties' => array('name' => 'urn:ogc:def:crs:OGC:1.3:CRS84')),
            'features' => [
                
            ]
        );

        
        // controlamos que el fid vaya aumentando con cada medición que añadimos 
        $fid = 1;

        // Creamos un array con datos de las mediciones que se añadirá a "features"
        while ($datos = $sentencia->fetch()) {
            // A partir del id de la medición genérica obtenemos el cenit en 90º
            $sql_cenit = "SELECT magnitud FROM medicion_individual WHERE generica_id={$datos['id']} and declinacion=90";
            $sentencia_cenit = $conn->prepare($sql_cenit);
            $sentencia_cenit->execute();
            $cenit = $sentencia_cenit->fetch();

            $features = [array('type' => 'Feature', 'properties' => array('fid' => $fid, 'cenit' => $cenit['magnitud'], 'nombre' => $datos['localizacion'], 'imagen' => 'uploads/mediciones/'.$datos['grafico'].'/'.$datos['grafico'].'.png'), 'geometry' => array('type' => 'Point', 'coordinates' => [$datos['longitud'], $datos['latitud']]))];    
            
            $fid = $fid+1;

            // Se concatena el array actual con lo que haya en la parte de features del json
            $datos_js['features'] = array_merge($datos_js['features'], $features);
        }

        $contenido .= json_encode($datos_js);

        // Se escribe en el archivo del cual lee qgis
        file_put_contents($rutaDatosMapas, $contenido);

        return $this->render('buscador/datos.html.twig', [
            'controller_name' => 'MapaQgisController',
            'activeInicio' => $activeInicio,
            'activeBusqueda' => $activeBusqueda,
            'activeContacto' => $activeContacto,
            'activeLogin' => $activeLogin,

            'logueado' => $logueado,
            'nick' => $nick,
            'nombrecompleto' => $nombrecompleto,
            'role' => $role,
            'fotoPerfil' => $fotoPerfil,
            'bio' => $bio,
        ]);
    }
}
