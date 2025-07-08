<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\DossierRepository;
use DateTime;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MessageController extends AbstractController
{

    //tous les messages
    #[Route('/message', name: 'app_message', methods: ['GET'])]
    public function index(MessageRepository $messageRepository,): JsonResponse
    {
        $messages = $messageRepository->findAll();
    

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'object' => $message->getObject(),
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'user' => [
                    'id' => $message->getUser()->getId(),
                    'last_name' => $message->getUser()->getLastname(),
                    'first_name' => $message->getUser()->getFirstname()],       

                   
                ];
//est ce que j inclus le dossier ici?            
            }      
            return $this->json($data, Response::HTTP_OK);        
    }


    //voir un message
     #[Route('/message/{id}', name: 'show_message', methods: ['GET'])]
    public function showMessage($id,MessageRepository $messageRepository): JsonResponse
    {
        $messages = $messageRepository->find($id);

        if (!$messages) {
            return $this->json(['message' => 'Message not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $messages->getId(),
            'object' => $messages->getObject(),
            'content' => $messages->getContent(),
            'createdAt' => $messages->getCreatedAt()->format('Y-m-d H:i:s'),
            'user' => [
                'id' => $messages->getUser()->getId(),
                'last_name' => $messages->getUser()->getLastName(),
                'first_name' => $messages->getUser()->getFirstName()],
        ];
        
            return $this->json($data, Response::HTTP_OK);
    } 

    // créer un message
    //   #[Route('/message/add', name: 'add_message', methods: ['POST'])]
    // public function addMessage(Request $request,EntityManagerInterface $entityManager): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);    //json_decode sert à convertir du JSON en tableau PHP ou en objet.
    //                                                           //true signifie : convertir en tableau associatif PHP, pas en objet.
    //     if (!isset($data['object']) || !isset($data['content']) ) {
    //         return $this->json(['message' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
    //     } 
        
    //     $message = new Message();
    //     $message->setObject($data['object']);
    //     $message->setContent($data['content']);
    //     $message->setCreatedAt(new DateTime());
    //     $message->setUser($this->getUser());// si user connecté
        

    //     $entityManager->persist($message);
    //     $entityManager->flush();    

    //     return $this->json(['message' => 'Message envoyé'], Response::HTTP_CREATED);
    // } 

    // modifier,mettre à jour un message
     #[Route('/message/edit/{id}', name: 'update_message', methods: ['PUT'])]
    public function updateMessage($id,Request $request,MessageRepository $messageRepository,EntityManagerInterface $entityManager): JsonResponse
    {
        $message = $messageRepository->find($id);

        if (!$message) {
            return $this->json(['message' => 'Message not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['object'])) {
            $message->setObject($data['object']);
        }
        if (isset($data['content'])) {
            $message->setContent($data['content']);
        }

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'id' => $message->getId(),
            'object' => $message->getObject(),
            'content' => $message->getContent(),
            'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            // 'user' => [
            //     'id' => $message->getUser()->getId(),
            //     'last_name' => $message->getUser()->getLastName(),
            //     'first_name' => $message->getUser()->getFirstName()],
        ], Response::HTTP_OK);
    } 

    // supprimer un message
     #[Route('/message/delete/{id}', name: 'delete_message', methods: ['DELETE'])]
    public function deleteMessage($id, MessageRepository $messageRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $message = $messageRepository->find($id);
        if (!$message) {
            return $this->json(['message' => 'Message not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($message);
        $entityManager->flush();
        return $this->json(['message' => 'Message supprimé avec succès'], Response::HTTP_OK);
    }
}