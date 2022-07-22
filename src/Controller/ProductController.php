<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="app_product")
     */
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    private $ProductRepository;

    public function __construct(ProductRepository $ProductRepository)
    {
        $this->ProductRepository = $ProductRepository;
    }

    /**
     * @Route("/Products/", name="add_Product", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $uuid = $data['uuid'];
        $productType = $data['productType'];
        $name = $data['name'];
        $description = $data['description'];
        $weight = $data['weight'];
        $enabled = $data['enabled'];

        /*if (empty($uuid) || empty($productType) || empty($name) || empty($description) || empty($weight) || empty($enabled)) {
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }*/

        $this->ProductRepository->saveProduct($uuid, $productType, $name, $description, $weight, $enabled);

        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }

    public function getProduct($uuid): JsonResponse
    {
        $product = $this->ProductRepository->findOneBy(['uuid' => $uuid]);

        $data = [
            'uuid' => $product->getUuid(),
            'productType' => $product->getProductType(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'weight' => $product->getWeight(),
            'enabled' => $product->isEnabled(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function getAllProducts(): JsonResponse
    {
        $products = $this->ProductRepository->findAll();

        $data= [];

        foreach ($products as $product) {
            $data[] = [
                'uuid' => $product->getUuid(),
                'productType' => $product->getProductType(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'weight' => $product->getWeight(),
                'enabled' => $product->isEnabled(),
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function update($id, Request $request): JsonResponse
    {
        $product = $this->ProductRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        empty($data['uuid']) ? true : $product->setUuid($data['uuid']);
        empty($data['productType']) ? true : $product->setProductType($data['productType']);
        empty($data['name']) ? true : $product->setName($data['name']);
        empty($data['description']) ? true : $product->setDescription($data['description']);
        empty($data['weight']) ? true : $product->setWeight($data['weight']);
        empty($data['enabled']) ? true : $product->setEnabled($data['enabled']);

        $updatedProduct = $this->ProductRepository->updateProduct($product);

        return new JsonResponse(['status' => 'Product updated'], Response::HTTP_OK);
    }

    public function delete($id): JsonResponse
    {
        $product = $this->ProductRepository->findOneBy(['id' => $id]);

        $this->ProductRepository->removeProduct($product);

        return new JsonResponse(['status' => 'Product deleted'], Response::HTTP_OK);
    }
}
