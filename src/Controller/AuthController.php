<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * Muestra el formulario para registrar un usuario.
     */
    #[Route('/register', name: 'register_form', methods: ['GET'])]
    public function showRegisterForm(): Response
    {
        // Renderiza la plantilla register.html.twig ubicada típicamente en templates/auth/
        // Ajusta la ruta a tu conveniencia.
        return $this->render('auth/register.html.twig');
    }

    /**
     * Procesa los datos del formulario o petición JSON y crea un usuario nuevo.
     */
    #[Route('/register', name: 'register_user', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Para peticiones JSON
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            $data = $request->request->all();
        }

    
        // Validaciones mínimas
        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json(['error' => 'Faltan campos de usuario o contraseña'], 400);
        }

        // Verificar si ya existe el usuario
        $existingUser = $entityManager->getRepository(Usuario::class)
            ->findOneBy(['username' => $data['username']]);

        if ($existingUser) {
            return $this->json(['error' => 'El nombre de usuario ya existe'], 400);
        }

        // Crear nueva entidad Usuario
        $user = new Usuario();
        $user->setUsername($data['username']);

        // Hashear la contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Persistir en la base de datos
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message'  => 'Usuario creado correctamente',
            'username' => $user->getUsername(),
        ], 201);
    }

    /**
     * Endpoint de Login que valida las credenciales de un usuario.
     */
    #[Route('/login', name: 'login_form', methods: ['GET'])]
    public function showLoginForm(): Response
    {
        // Renderizamos la plantilla login.html.twig
        return $this->render('auth/login.html.twig');
    }



    #[Route('/login', name: 'login_user', methods: ['POST'])]
    public function login(
        Request $request,
        UsuarioRepository $usuarioRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!$data || !is_array($data)) {
            $data = $request->request->all();
        }

        // Validaciones básicas
        if (!isset($data['username']) || !isset($data['password'])) {
            return $this->json(['error' => 'Faltan campos de usuario o contraseña'], 400);
        }

        // Buscar al usuario por username
        $user = $usuarioRepository->findOneBy(['username' => $data['username']]);

        if (!$user) {
            return $this->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Verificar contraseña
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Aquí, en una aplicación real, se genera un token JWT o  la autenticación de Symfony con sesión
        return $this->json([
            'message'  => 'Login exitoso',
            'username' => $user->getUsername(),
            'roles'    => $user->getRoles(),
        ], 200);
    }
}