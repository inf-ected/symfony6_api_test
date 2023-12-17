<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Book;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api', name:'api_')]
class BookController extends AbstractController
{
    #[IsGranted('ROLE_API')]
    #[Route('/books', name:'books_create', methods: ['post'])]
    #[OA\Tag(name: 'books_create')]
    #[OA\Response(
        response: 200,
        description: 'Returns added book with id',
        content: new Model(type: Book::class)
    )]
    #[Security(name: 'basicAuth')]
    public function create(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator):JsonResponse
    {
        $book = new Book();
        $entityManager = $doctrine->getManager();

        $book->setTitle($request->request->get('title'));
        $book->setAuthor($request->request->get('author'));
        $book->setDescription($request->request->get('description'));
        $book->setPrice($request->request->get('price'));

        // Validation
        $errors = $validator->validate($book);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 400);
        }

        $entityManager->persist($book);
        $entityManager->flush();

        $data = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'price' => $book->getPrice(),
        ];

        return $this->json($data);
    }

    #[Route('/books', name: 'books_index', methods: ['get'])]
    #[OA\Tag(name: 'books_index')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of all books',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Book::class, groups: ['full']))
        )
    )]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        //2DO: пока нет пагинации 
        // $page = $request->request->get('page');
        $page = 1;
        $pageSize = 1000; 

        $books = $doctrine
            ->getRepository(Book::class)
            // ->findAll();
            ->findBy([], null, $pageSize, ($page - 1) * $pageSize);

        $data = [];

        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'description' => $book->getDescription(),
                'price' => $book->getPrice(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/books/{id}', name: 'book_show', methods: ['get'])]
    #[OA\Tag(name: 'book_show')]
    #[OA\Response(
        response: 200,
        description: 'Returns book by specific id',
        content: new Model(type: Book::class)
    )]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $book = $doctrine
            ->getRepository(Book::class)
            ->find($id);

        if (!$book) {
            return $this->json("No book found with id:" . $id, 404);
        }

        $data = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'price' => $book->getPrice(),
        ];

        return $this->json($data);
    }

    #[IsGranted('ROLE_API')]
    #[Route('/books/{id}', name: 'book_update', methods: ['put', 'patch'])]
    #[OA\Tag(name: 'book_update')]
    #[OA\Response(
        response: 200,
        description: 'Returns updated book',
        content: new Model(type: Bool::class)
    )]
    #[Security(name: 'basicAuth')]
    public function update(
        ManagerRegistry $doctrine, 
        Request $request, 
        int $id,
        ValidatorInterface $validator
        ): JsonResponse
    {
        $requestData = $request->getPayload();
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json("No book found with id:" . $id, 404);
        }
        // 2DO: вынести логику в отдельные методы сравнения объектов модели && обновлять только измененные поля 
        $updatedBook = new Book();
        $updatedBook->setTitle($requestData->get('title'));
        $updatedBook->setAuthor($requestData->get('author'));
        $updatedBook->setDescription($requestData->get('description'));
        $updatedBook->setPrice($requestData->get('price'));

        $errors = $validator->validate($updatedBook);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 400);
        }

        $book->setTitle($requestData->get('title'));
        $book->setAuthor($requestData->get('author'));
        $book->setDescription($requestData->get('description'));
        $book->setPrice($requestData->get('price'));
        $entityManager->flush();

        $data = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'author' => $book->getAuthor(),
            'description' => $book->getDescription(),
            'price' => $book->getPrice(),
        ];
        return $this->json($data);
    }

    #[IsGranted('ROLE_API')]
    #[Route('/books/{id}', name: 'book_delete', methods: ['delete'])]
    #[OA\Tag(name: 'book_delete')]
    #[OA\Response(
        response: 200,
        description: 'Deleted successfully book with ID',
        content: new OA\JsonContent(
            type: 'string',
            example: "Deleted successfully book with id:123"
        )
    )]
    #[Security(name: 'basicAuth')]
    public function delete(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book) {
            return $this->json("No book found with id:" . $id, 404);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        if (!$book) {
            return $this->json("No book found with id:" . $id, 404);
        }

        return $this->json("Deleted successfully book with id:" . $id, 200);
    }
}