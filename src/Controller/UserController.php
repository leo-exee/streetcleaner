<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\DeviceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{


    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, DeviceRepository $deviceRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if($user === $this->getUser() || $securityContext->isGranted('ROLE_ADMIN')){

            $devices = $deviceRepository->findBy(['user' => $user]);

            $temperatures = array_map(function($device) {
                return $device->getTemperature();
            }, $devices);

            $humidities = array_map(function($device) {
                return $device->getHumidity();
            }, $devices);

            if(count($temperatures) > 0 and count($humidities) > 0){
                $temp = array_sum($temperatures) / count($temperatures);
                $hum = array_sum($humidities) / count($humidities);

                return $this->render('user/show.html.twig', [
                    'user' => $user,
                    'devices' => $devices,
                    'temp' => $temp,
                    'hum' => $hum
                ]);
            } else{
                return $this->render('user/show.html.twig', [
                    'user' => $user,
                    'devices' => $devices
                ]);
            }


        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if($user === $this->getUser() || $securityContext->isGranted('ROLE_ADMIN')){
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userRepository->save($user, true);

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('{id}/delete', name: 'app_user_remove')]
    public function remove (Request $request, User $user)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN') || $this->getUser() === $user) {

            return $this->render('user/remove.html.twig', [
                'user' => $user
            ]);

        }
        return $this->redirectToRoute('app_user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if($user === $this->getUser() || $securityContext->isGranted('ROLE_ADMIN')){
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $userRepository->remove($user, true);
            }

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);

    }
}
