<?php

namespace App\Controller;

use App\Entity\Services;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/services', name: 'api_services_')]
class ServicesController extends AbstractController
{
    public function __construct(
        private ServicesRepository $servicesRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function getServices(Request $request): JsonResponse
    {
        $actif = $request->query->get('actif');
        
        $queryBuilder = $this->servicesRepository->createQueryBuilder('s');

        if ($actif !== null) {
            $queryBuilder->andWhere('s.actif = :actif')
                        ->setParameter('actif', filter_var($actif, FILTER_VALIDATE_BOOLEAN));
        }

        $queryBuilder->orderBy('s.ordreAffichage', 'ASC');

        $services = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($services, 'json', [
            'groups' => ['services:read']
        ]);

        return new JsonResponse([
            'services' => json_decode($data, true),
            'total' => count($services),
            'status' => count($services) > 0 ? 'found' : 'empty'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    public function getService(int $id): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        
        if (!$service) {
            return new JsonResponse(['message' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($service, 'json', [
            'groups' => ['services:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function createService(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $service = new Services();
        $service->setTitre($data['titre'] ?? '');
        $service->setDescription($data['description'] ?? null);
        $service->setIcone($data['icone'] ?? null);
        $service->setFonctionnalites($data['fonctionnalites'] ?? null);
        $service->setGammePrix($data['gamme_prix'] ?? null);
        $service->setOrdreAffichage($data['ordre_affichage'] ?? 0);
        $service->setActif($data['actif'] ?? true);

        $errors = $this->validator->validate($service);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($service, 'json', [
            'groups' => ['services:read']
        ]);

        return new JsonResponse(json_decode($data, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function updateService(int $id, Request $request): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        
        if (!$service) {
            return new JsonResponse(['message' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['titre'])) $service->setTitre($data['titre']);
        if (isset($data['description'])) $service->setDescription($data['description']);
        if (isset($data['icone'])) $service->setIcone($data['icone']);
        if (isset($data['fonctionnalites'])) $service->setFonctionnalites($data['fonctionnalites']);
        if (isset($data['gamme_prix'])) $service->setGammePrix($data['gamme_prix']);
        if (isset($data['ordre_affichage'])) $service->setOrdreAffichage($data['ordre_affichage']);
        if (isset($data['actif'])) $service->setActif($data['actif']);

        $errors = $this->validator->validate($service);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($service, 'json', [
            'groups' => ['services:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteService(int $id): JsonResponse
    {
        $service = $this->servicesRepository->find($id);
        
        if (!$service) {
            return new JsonResponse(['message' => 'Service non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($service);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Service supprimé avec succès']);
    }
}
