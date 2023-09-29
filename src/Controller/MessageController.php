<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\MessagesRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MessageController extends AbstractController
{
    #[Route('/send-message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em)
    {
        $idSender = $request->request->get('senderId');
        $idReceiver = $request->request->get('receiverId');
        $messages = $request->request->get('messages');
        $dateMessage = new DateTime();

        $message = new Messages();

        $message->setSenderId($idSender);
        $message->setReceiverId($idReceiver);
        $message->setMessages($messages);
        $message->setDateMessage($dateMessage);

        $em->persist($message);
        $em->flush();

        return $this->json([
            'status' => 200,
            'data' => $message->getMessages()
        ]);
    }

    #[Route('/box-message/{id}', name: 'box_message', methods: ['GET'])]
    public function getLastMessagesForUsers(EntityManagerInterface $em)
    {
        $queryBuilder = $em->createQueryBuilder();

        //cette requête prend les dernières messages de l'utilisateur avec un autre utilisateur
        $queryBuilder
            ->select('m')
            ->from(Messages::class, 'm')
            ->leftJoin(Messages::class, 'm2', 'WITH', 'm.senderId = m2.senderId AND m.receiverId = m2.receiverId AND m.dateMessage < m2.dateMessage')
            ->where('m2.dateMessage IS NULL');

        $query = $queryBuilder->getQuery();
        $messages = $query->getResult();

        $messageData = [];
        foreach ($messages as $message) {
            $messageData[] = [
                'id' => $message->getId(),
                'senderId' => $message->getSenderId(),
                'receiverId' => $message->getReceiverId(),
                'dateMessage' => $message->getDateMessage(),
                'messages' => $message->getMessages()
            ];
        }

        return $this->json([
            'status' => 200,
            'data' => $messageData
        ]);
    }

    #[Route('/detail-message/{myId}/{idOther}', name: 'detail_message', methods: ['GET'])]
    public function getMessageWithOther($myId, $idOther, EntityManagerInterface $em)
    {
        $queryBuilder = $em->createQueryBuilder();

        //cette requête prend les discussions entre chaques utilisateurs
        $queryBuilder
            ->select('m')
            ->from(Messages::class, 'm')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('m.senderId', ':senderId1'),
                    $queryBuilder->expr()->eq('m.receiverId', ':receiverId1')
                ),
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('m.senderId', ':senderId2'),
                    $queryBuilder->expr()->eq('m.receiverId', ':receiverId2')
                )
            )
            ->setParameter('senderId1', $myId)
            ->setParameter('receiverId1', $idOther)
            ->setParameter('senderId2', $myId)
            ->setParameter('receiverId2', $idOther);

        $query = $queryBuilder->getQuery();
        $messages = $query->getResult();

        $list = [];

        foreach ($messages as $message) {
            $list[] = [
                'id' => $message->getId(),
                'senderId' => $message->getSenderId(),
                'receiverId' => $message->getReceiverId(),
                'dateMessage' => $message->getDateMessage(),
                'messages' => $message->getMessages()
            ];
        }

        return $this->json($list);
    }
}
