<?php

namespace App\Controller;

use App\Entity\MessagesContact;
use App\Repository\MessagesContactRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact', name: 'api_contact_')]
class ContactController extends AbstractController
{
    public function __construct(
        private MessagesContactRepository $messagesRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EmailService $emailService
    ) {}

    #[Route('/messages', name: 'messages_list', methods: ['GET'])]
    public function getMessages(Request $request): JsonResponse
    {
        $statut = $request->query->get('statut');
        $page = (int) $request->query->get('page', 1);
        $limit = (int) $request->query->get('limit', 10);

        $queryBuilder = $this->messagesRepository->createQueryBuilder('m');

        if ($statut) {
            $queryBuilder->andWhere('m.statut = :statut')
                        ->setParameter('statut', $statut);
        }

        $queryBuilder->orderBy('m.createdAt', 'DESC')
                    ->setFirstResult(($page - 1) * $limit)
                    ->setMaxResults($limit);

        $messages = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->serialize($messages, 'json', [
            'groups' => ['messages:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/messages/{id}', name: 'message_get', methods: ['GET'])]
    public function getMessage(int $id): JsonResponse
    {
        $message = $this->messagesRepository->find($id);
        
        if (!$message) {
            return new JsonResponse(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($message, 'json', [
            'groups' => ['messages:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/send', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $message = new MessagesContact();
        $message->setNomExpediteur($data['nom_expediteur'] ?? '');
        $message->setEmailExpediteur($data['email_expediteur'] ?? '');
        $message->setSujet($data['sujet'] ?? '');
        $message->setMessage($data['message'] ?? '');
        $message->setStatut('nouveau');

        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        // Sauvegarder le message en base
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        // Envoyer les emails
        $emailSent = $this->emailService->sendContactNotification([
            'nom_expediteur' => $message->getNomExpediteur(),
            'email_expediteur' => $message->getEmailExpediteur(),
            'sujet' => $message->getSujet(),
            'message' => $message->getMessage()
        ]);

        $confirmationSent = $this->emailService->sendContactConfirmation(
            $message->getEmailExpediteur(),
            [
                'nom_expediteur' => $message->getNomExpediteur(),
                'sujet' => $message->getSujet(),
                'message' => $message->getMessage()
            ]
        );

        $responseData = $this->serializer->serialize($message, 'json', [
            'groups' => ['messages:read']
        ]);

        $response = json_decode($responseData, true);
        $response['email_sent'] = $emailSent;
        $response['confirmation_sent'] = $confirmationSent;

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    #[Route('/messages/{id}/status', name: 'update_status', methods: ['PUT'])]
    public function updateMessageStatus(int $id, Request $request): JsonResponse
    {
        $message = $this->messagesRepository->find($id);
        
        if (!$message) {
            return new JsonResponse(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        
        if (isset($data['statut'])) {
            $message->setStatut($data['statut']);
        }

        $errors = $this->validator->validate($message);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $data = $this->serializer->serialize($message, 'json', [
            'groups' => ['messages:read']
        ]);

        return new JsonResponse(json_decode($data, true));
    }

    #[Route('/messages/{id}', name: 'delete_message', methods: ['DELETE'])]
    public function deleteMessage(int $id): JsonResponse
    {
        $message = $this->messagesRepository->find($id);
        
        if (!$message) {
            return new JsonResponse(['message' => 'Message non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($message);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Message supprimé avec succès']);
    }

    #[Route('/project-inquiry', name: 'project_inquiry', methods: ['POST'])]
    public function sendProjectInquiry(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validation des données requises
        $requiredFields = ['nom', 'email', 'type_projet', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "Le champ '{$field}' est requis"], Response::HTTP_BAD_REQUEST);
            }
        }

        // Envoyer l'email de demande de projet
        $emailSent = $this->emailService->sendProjectInquiry('contact@armandadjibako.com', [
            'nom' => $data['nom'],
            'email' => $data['email'],
            'entreprise' => $data['entreprise'] ?? '',
            'budget' => $data['budget'] ?? 'Non spécifié',
            'type_projet' => $data['type_projet'],
            'date_souhaitee' => $data['date_souhaitee'] ?? 'Non spécifiée',
            'description' => $data['description']
        ]);

        // Envoyer une confirmation à l'expéditeur
        $confirmationSent = $this->emailService->sendProjectConfirmation(
            $data['email'],
            [
                'nom' => $data['nom'],
                'email' => $data['email'],
                'entreprise' => $data['entreprise'] ?? '',
                'budget' => $data['budget'] ?? 'Non spécifié',
                'type_projet' => $data['type_projet'],
                'date_souhaitee' => $data['date_souhaitee'] ?? 'Non spécifiée',
                'description' => $data['description']
            ]
        );

        return new JsonResponse([
            'success' => true,
            'message' => 'Demande de projet envoyée avec succès',
            'email_sent' => $emailSent,
            'confirmation_sent' => $confirmationSent
        ], Response::HTTP_CREATED);
    }
}
