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

final class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/insertarAutor', name: 'insertar_autor')]
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
    public function verAutores(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Autor::class);
        $autores = $repo->findAll();

        return $this->render('autores.html.twig', [
            'autores' => $autores,
        ]);
    }

    #[Route('/libros', name: 'app_books')]
    public function verLibros(EntityManagerInterface $em): Response
    {
        $repo = $em->getRepository(Libro::class);
        $libros = $repo->findAll();

        return $this->render('libros.html.twig', [
            'libros' => $libros,
        ]);
    }

    #[Route('/insertarLibro', name: 'insertar_libro')]
    public function insertarLibro(Request $request, EntityManagerInterface $em): Response
    {
        $libro = new Libro();
        $form = $this->createForm(LibroType::class, $libro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($libro);
            $em->flush();
            return $this->redirectToRoute('app_books');
        }

        return $this->render('insertarlib.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
