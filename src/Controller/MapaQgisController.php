<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapaQgisController extends AbstractController
{
    #[Route('/mapa/qgis', name: 'mapa_qgis')]
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

        /*$datos_js = '{
            "type": "FeatureCollection",
            "name": "medicionesCielo_5",
            "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
            "features": [
                { "type": "Feature", "properties": { "fid": "1", "cenit": 19.0, "nombre": "Carretera Cabra", "imagen": "images/foto360.jpg" }, "geometry": { "type": "Point", "coordinates": [ -3.710803906867161, 37.012163727462713 ] } },
                { "type": "Feature", "properties": { "fid": "2", "cenit": 20.2, "nombre": "El Purche", "imagen": "images/foto360.jpg" }, "geometry": { "type": "Point", "coordinates": [ -3.503842193736335, 37.152616166748437 ] } },
                { "type": "Feature", "properties": { "fid": "3", "cenit": 21.3, "nombre": "Hoya de la Mora", "imagen": "images/foto360.jpg" }, "geometry": { "type": "Point", "coordinates": [ -3.386826862759218, 37.093557861530513 ] } },
                { "type": "Feature", "properties": { "fid": "4", "cenit": 21.4, "nombre": "Gorafe", "imagen": "images/foto360.jpg" }, "geometry": { "type": "Point", "coordinates": [ -3.05099455504029, 37.511294999081414 ] } },
                { "type": "Feature", "properties": { "fid": "5", "cenit": 21.0, "nombre": "La Sagra", "imagen": "images/foto360.jpg" }, "geometry": { "type": "Point", "coordinates": [ -2.522884610561774, 37.979423547228635 ] } }
            ]
        }';*/

        // Accedemos a la base de datos para obtener las mediciones
        $entityManager = $this->getDoctrine()->getManager();
        $conn = $entityManager->getConnection();

        $sql_datos = "SELECT * FROM medicion_generica";
        $sql_filas = "SELECT COUNT(*) FROM medicion_generica";

        // Se ejecuta la sentencia SQL
        $sentencia = $conn->prepare($sql_datos);
        $sentencia->execute();
        //$datos = $sentencia->fetchAll();

        /*$sentencia = $conn->prepare($sql_filas);
        $sentencia->execute();
        $filas = $sentencia->fetchAll();*/

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
            $features = [array('type' => 'Feature', 'properties' => array('fid' => $fid, 'cenit' => 19.0, 'nombre' => $datos['localizacion'], 'imagen' => 'uploads/mediciones/'.$datos['grafico'].'/'.$datos['grafico'].'.png'), 'geometry' => array('type' => 'Point', 'coordinates' => [$datos['longitud'], $datos['latitud']]))];    
            
            $fid = $fid+1;
            $datos_js['features'] = array_merge($datos_js['features'], $features);
        }

        $contenido .= json_encode($datos_js);

        file_put_contents($rutaDatosMapas, $contenido);

        return $this->render('mapa_qgis/index.html.twig', [
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
