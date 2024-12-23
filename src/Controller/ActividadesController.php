<?php

namespace App\Controller;

use App\Entity\Actividad;
use App\Repository\ActividadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ActividadesController extends AbstractController
{
    #[Route('/actividades', methods: ['GET'])]
    public function listarActividades(ActividadRepository $actividadRepository): JsonResponse
    {
        $actividades = $actividadRepository->findAll();
        $data = array_map(fn($a) => [
            'id' => $a->getId(),
            'nombre' => $a->getNombre(),
            'descripcion_corta' => $a->getDescripcionCorta(),
            'precio' => $a->getPrecio(),
        ], $actividades);

        return $this->json($data);
    }

    #[Route('/actividades/detalle-proveedores', methods: ['GET'])]
     public function listarActividadesConProveedores(ActividadRepository $actividadRepository): JsonResponse
    {
    $actividades = $actividadRepository->findAll();

    $data = array_map(function ($actividad) {
        
        $proveedor = $actividad->getProveedor();

        $proveedorData = $proveedor ? [
            'id' => $proveedor->getId(),
            'nombre' => $proveedor->getNombre(),
        ] : null;

        return [
            'id' => $actividad->getId(),
            'nombre' => $actividad->getNombre(),
            'descripcion_corta' => $actividad->getDescripcionCorta(),
            'precio' => $actividad->getPrecio(),
            'tiene_proveedor' => $proveedor !== null,
            'proveedor' => $proveedorData,
        ];
    }, $actividades);

    return $this->json($data);
    }



    #[Route('/actividades/buscar/{nombre}', methods: ['GET'])]
    public function buscarPorNombre(ActividadRepository $actividadRepository, string $nombre): JsonResponse
    {
        
        $actividades = $actividadRepository->createQueryBuilder('a')
            ->where('a.nombre LIKE :nombre')
            ->setParameter('nombre', '%' . $nombre . '%')
            ->getQuery()
            ->getResult();

        
        if (!$actividades) {
            return $this->json(['error' => 'No se encontraron actividades con ese nombre'], 404);
        }

        
        $data = array_map(fn($a) => [
            'id' => $a->getId(),
            'nombre' => $a->getNombre(),
            'descripcion_corta' => $a->getDescripcionCorta(),
            'precio' => $a->getPrecio(),
        ], $actividades);

        return $this->json($data);
    }



    #[Route('/actividades/{id<\d+>}', methods: ['GET'])]
    public function detalleActividad(ActividadRepository $actividadRepository, int $id): JsonResponse
    {
        $actividad = $actividadRepository->find($id);

        if (!$actividad) {
            return $this->json(['error' => 'Actividad no encontrada'], 404);
        }

        return $this->json([
            'id' => $actividad->getId(),
            'nombre' => $actividad->getNombre(),
            'descripcion_corta' => $actividad->getDescripcionCorta(),
            'descripcion_larga' => $actividad->getDescripcionLarga(),
            'precio' => $actividad->getPrecio(),
        ]);
    }

    
}

