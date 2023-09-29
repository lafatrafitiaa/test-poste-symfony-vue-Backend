<?php

namespace App\Controller;

use App\Entity\Friends;
use App\Repository\FriendsRepository;
use App\Repository\UsersRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/user/{id<\d+>?}', 'user_by_id', methods: ['GET'])]
    public function getUserById($id, UsersRepository $usersRepository)
    {
        $user = $usersRepository->findAll();

        if ($id != null) {
            $user = $usersRepository->find($id);
        }

        $data = [];
        foreach ($user as $u) {
            $data = [
                'id' => $u->getId(),
                'email' => $u->getEmail(),
                'username' => $u->getUsername()
            ];
        }

        $status = 200;
        if (!$user) {
            $status = 404;
        }

        return $this->json([
            'status' => $status,
            'message' => $data
        ]);
    }

    //Le procédure d'une demande d'ami
    #[Route('/friend-request', name: 'friend-request', methods: ['POST'])]
    public function friendRequest(Request $request, EntityManagerInterface $em)
    {
        $friend = new Friends();
        $friend->setMyId($request->request->get('myId'));
        $friend->setFirendId($request->request->get('fiendId'));
        $friend->setDateRequest(new DateTime());
        $friend->setIsAccepted(false);

        $em->persist($friend);
        $em->flush();

        return $this->json([
            'status' => 200,
            'message' => "Success to add friend."
        ]);
    }

    #[Route('/accept-friend', name: 'accept_friend', methods: ['POST'])]
    public function acceptFriend(Request $request, EntityManagerInterface $em, FriendsRepository $friendsRepository)
    {
        $myId = $request->request->get('myId');
        $friendId = $request->request->get('friendId');

        $friend = $friendsRepository->findBy([
            'myId' => $myId,
            'friendId' => $friendId
        ]);

        $friend[0]->setIsAccepted(true);

        $em->persist($friend[0]);
        $em->flush();

        return $this->json([
            'status' => 200,
            'message' => 'You are friends'
        ]);
    }
}
