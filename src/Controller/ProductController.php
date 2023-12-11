<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use DateTimeImmutable;

class ProductController extends AbstractController
{

    #[Route('/products', name: 'product_index', methods: ['get'])]
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $products = $doctrine
            ->getRepository(Product::class)
            ->findAll();

        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'productName' => $product->getProductName(),
                'description' => $product->getDescription(),
                'createdAt' => $product->getCreatedAt(),
                'updatedAt' => $product->getUpdatedAt(),
            ];
        }

        return $this->json($data);
    }

    public function sanitizeSku(?string $sku): ?string
    {
        if (null === $sku) return null;


        $sku = trim($sku);
        $sku = strtoupper($sku);

        if (strlen($sku) > 255) {
            throw new \InvalidArgumentException('El SKU debe tener una longitud máxima de 255 caracteres.');
        }

        return $sku;
    }

    #[Route('/products', name: 'product_create', methods: ['post'])]
    public function create(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $requestDataArray = json_decode($request->getContent(), true);

        $responseData = [];

        foreach ($requestDataArray as $requestData) {
            $product = new Product();

            $product->setSku($requestData['sku'] ?? null);
            $product->setProductName($requestData['productName'] ?? null);
            $product->setDescription($requestData['description'] ?? null);

            if (isset($requestData['createdAt'])) {
                $createdAt = DateTimeImmutable::createFromFormat('Y-m-d', $requestData['createdAt']);
                if ($createdAt instanceof DateTimeImmutable) {
                    $product->setCreatedAt($createdAt);
                } else {
                    return $this->json(['error' => 'Formato de fecha no válido para createdAt'], Response::HTTP_BAD_REQUEST);
                }
            }

            if (isset($requestData['updatedAt'])) {
                $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d', $requestData['updatedAt']);
                if ($updatedAt instanceof DateTimeImmutable) {
                    $product->setUpdatedAt($updatedAt);
                } else {
                    return $this->json(['error' => 'Formato de fecha no válido para updatedAt'], Response::HTTP_BAD_REQUEST);
                }
            }

            $entityManager->persist($product);

            $responseData[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'productName' => $product->getProductName(),
                'description' => $product->getDescription(),
                'createdAt' => $product->getCreatedAt(),
                'updatedAt' => $product->getUpdatedAt(),
            ];
        }

        $entityManager->flush();

        return $this->json($responseData);
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['get'])]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $product = $doctrine->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json('No product found for id ' . $id, 404);
        }

        $data =  [
            'id' => $product->getId(),
            'sku' => $product->getSku(),
            'productName' => $product->getProductName(),
            'description' => $product->getDescription(),
            'createdAt' => $product->getCreatedAt(),
            'updatedAt' => $product->getUpdatedAt(),
        ];

        return $this->json($data);
    }


    #[Route('/products', name: 'product_update', methods: ['put'])]
    public function update(ManagerRegistry $doctrine, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $requestDataArray = json_decode($request->getContent(), true);

        $responseData = [];

        foreach ($requestDataArray as $requestData) {
            $sku = $requestData['sku'] ?? null;

            if (!$sku) {
                return $this->json(['error' => 'El SKU es obligatorio para actualizar un producto'], Response::HTTP_BAD_REQUEST);
            }

            $product = $entityManager->getRepository(Product::class)->findOneBy(['sku' => $sku]);

            if (!$product) {
                return $this->json(['error' => 'Producto no encontrado con SKU ' . $sku], Response::HTTP_NOT_FOUND);
            }

            $product->setProductName($requestData['productName'] ?? $product->getProductName());
            $product->setDescription($requestData['description'] ?? $product->getDescription());

            if (isset($requestData['createdAt'])) {
                $createdAt = DateTimeImmutable::createFromFormat('Y-m-d', $requestData['createdAt']);
                if ($createdAt instanceof DateTimeImmutable) {
                    $product->setCreatedAt($createdAt);
                } else {
                    return $this->json(['error' => 'Formato de fecha no válido para createdAt'], Response::HTTP_BAD_REQUEST);
                }
            }

            if (isset($requestData['updatedAt'])) {
                $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d', $requestData['updatedAt']);
                if ($updatedAt instanceof DateTimeImmutable) {
                    $product->setUpdatedAt($updatedAt);
                } else {
                    return $this->json(['error' => 'Formato de fecha no válido para updatedAt'], Response::HTTP_BAD_REQUEST);
                }
            }

            $responseData[] = [
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'productName' => $product->getProductName(),
                'description' => $product->getDescription(),
                'createdAt' => $product->getCreatedAt(),
                'updatedAt' => $product->getUpdatedAt(),
            ];
        }

        $entityManager->flush();

        return $this->json($responseData);
    }
}
