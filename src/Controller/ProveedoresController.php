<?php

namespace App\Controller;

use App\Entity\Proveedor;
use App\Repository\ProveedorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProveedoresController extends AbstractController
{
    #[Route('/proveedores/', methods: ['GET'])]
    public function listarProveedores(ProveedorRepository $proveedorRepository): JsonResponse
    {
        $proveedores = $proveedorRepository->findAll();
        $data = array_map(fn($p) => [
            'id' => $p->getId(),
            'nombre' => $p->getNombre(),
            'tiene_actividades' => $p->getActividad()->count() > 0,
        ], $proveedores);

        return $this->json($data);
    }
}

