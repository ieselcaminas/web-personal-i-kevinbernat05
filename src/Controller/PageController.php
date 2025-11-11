<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Autor;
use App\Form\AutorType;
use App\Entity\Libro;
use App\Form\LibroType;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(Request $request): Response
    {
        $this->addFlash('success', 'Inicio de sesión exitoso');
        return $this->render('index.html.twig');
    }

    #[Route('/insertarAutor', name: 'insertar_autor')]
    #[IsGranted('ROLE_USER')]
    public function insertarAutor(Request $request, EntityManagerInterface $em): Response
    {

        $autor = new Autor();
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($autor);
            $em->flush();

            return $this->redirectToRoute('app_authors'); // ahora redirige a la lista
        }

        return $this->render('insertaraut.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/eliminarAutor', name: 'eliminar_autor')]
    #[IsGranted('ROLE_USER')]
    public function eliminarAutor(Request $request, EntityManagerInterface $em): Response    {
    $id = $request->request->get('autor_id');
    $autor = $em->getRepository(Autor::class)->find($id);

    if ($autor) { 
        $em->remove($autor);
        $em->flush();
    }

    return $this->redirectToRoute('app_authors');
    }   

    #[Route('/autores', name: 'app_authors')]
    #[IsGranted('ROLE_USER')]
    public function verAutores(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Autor::class);
        $autores = $repo->findAll();

        return $this->render('autores.html.twig', [
            'autores' => $autores,
        ]);
    }

    #[Route('/editarAutor/{id}', name: 'editar_autor')]
    #[IsGranted('ROLE_USER')]
    public function editarAutor(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $autor = $em->getRepository(Autor::class)->find($id);
        if (!$autor) {
            throw $this->createNotFoundException('Autor no encontrado');
        }
        $form = $this->createForm(AutorType::class, $autor);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_authors');
        }
        return $this->render('editaraut.html.twig', [
            'form' => $form->createView(),
            'autor' => $autor,
        ]);
    }

    #[Route('/buscarAutores', name: 'buscar_autores', methods: ['GET']  )]
    #[IsGranted('ROLE_USER')]
    public function buscarAutores(Request $request, EntityManagerInterface $em): Response
    {
    $termino = $request->query->get('q', '');
    $autores = [];
    if ($termino) {
        $autores = $em->getRepository(Autor::class)->createQueryBuilder('a')
            ->where('a.nombre LIKE :termino')
            ->setParameter('termino', '%' . $termino . '%')
            ->getQuery()
            ->getResult();
    }
    return $this->render('buscar_autor.html.twig', [
        'autores' => $autores,
        'termino' => $termino,
    ]);
    }   

    #[Route('/libros', name: 'app_books')]
    #[IsGranted('ROLE_USER')]
    public function verLibros(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Libro::class);
        $libros = $repo->findAll();

        return $this->render('libros.html.twig', [
            'libros' => $libros,
        ]);
    }

    #[Route('/insertarLibro', name: 'insertar_libro')]
    #[IsGranted('ROLE_USER')]
    public function insertarLibro(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
    $libro = new Libro();
    $form = $this->createForm(LibroType::class, $libro);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $imagenFile */
        $imagenFile = $form->get('imagen')->getData();

        if ($imagenFile) {
            $originalFilename = pathinfo($imagenFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imagenFile->guessExtension();

            // Mueve el archivo a la carpeta uploads/libros
            $imagenFile->move(
                $this->getParameter('libros_directory'),
                $newFilename
            );

            // Guarda solo el nombre del archivo en la entidad
            $libro->setImagen($newFilename);
        }

        $em->persist($libro);
        $em->flush();

        return $this->redirectToRoute('app_books');
    }

    return $this->render('insertarlib.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/eliminarLibro', name: 'eliminar_libro', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function eliminarLibro(Request $request, EntityManagerInterface $em): Response
    {
    $id = $request->request->get('libro_id');
    $libro = $em->getRepository(Libro::class)->find($id);

    if ($libro) {
        $em->remove($libro);
        $em->flush();
    }

    return $this->redirectToRoute('app_books');
    }

    #[Route('/editarLibro/{id}', name: 'editar_libro')]
    #[IsGranted('ROLE_USER')]
    public function editarLibro(Request $request, EntityManagerInterface $em, int $id): Response
    {
        $libro = $em->getRepository(Libro::class)->find($id);
        if (!$libro) {
            throw $this->createNotFoundException('Libro no encontrado');
        }
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_books');
        }
        return $this->render('editarlib.html.twig', [
            'form' => $form->createView(),
            'libro' => $libro,
        ]);
    }

    #[Route('/buscarLibros', name: 'buscar_libros', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function buscarLibros(Request $request, EntityManagerInterface $em): Response
    {
    $termino = $request->query->get('q', '');
    
    $libros = [];
    if ($termino) {
        $libros = $em->getRepository(Libro::class)->createQueryBuilder('l')
            ->where('l.titulo LIKE :termino')
            ->setParameter('termino', '%' . $termino . '%')
            ->getQuery()
            ->getResult();
    }

    return $this->render('buscar_libros.html.twig', [
        'libros' => $libros,
        'termino' => $termino,
    ]);
    }

    #[Route('/api/autores', name: 'api_autores_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function apiCreateAutor(Request $request, EntityManagerInterface $em): JsonResponse
    {
    $nombre = $request->request->get('nombre');
    $fecha = $request->request->get('fechaNacimiento');
    $genero = $request->request->get('genero');

    if (!$nombre || !$fecha || !$genero) {
        return new JsonResponse(['error' => 'Faltan datos obligatorios'], 400);
    }

    try {
        $fechaNacimiento = new \DateTime($fecha);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => 'Formato de fecha inválido'], 400);
    }

    try {
        $autor = new Autor();
        $autor->setNombre($nombre);
        $autor->setGenero($genero);
        $autor->setFechaNacimiento($fechaNacimiento);

        $em->persist($autor);
        $em->flush();

        return new JsonResponse([
            'id' => $autor->getId(),
            'nombre' => $autor->getNombre()
        ], 201);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], 500);
    }
}


}
