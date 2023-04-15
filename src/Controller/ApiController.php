<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\DeviceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $encoderService): Response {

        $user = $userRepository->findOneBy(['email' => $request->get('email')]);
        $password = $request->get('password');

        if($encoderService->isPasswordValid($user, $password)) {

            $data = array(
                'email' => $request->get('email'),
                'password' => $request->get('password'),
            );

            return $this->json(
                $data,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
        return $this->json([false]);
    }

    #[Route('/get', name: 'api_get_datas', methods: ['POST'])]
    public function get(Request $request, UserRepository $userRepository, DeviceRepository $deviceRepository, UserPasswordHasherInterface $encoderService): Response {

        $user = $userRepository->findOneBy(['email' => $request->get('email')]);
        $password = $request->get('password');

        if($encoderService->isPasswordValid($user, $password)) {

            $devices = $deviceRepository->findBy(['user' => $user]);

            $devicesFiltered = array_filter($devices, function($d) {
                return $d->getTemperature() !== null && $d->getHumidity() !== null;
            });

            $data = array();

            foreach($devicesFiltered as $device) {
                $data[] = array(
                    'address' => strval($device->getAdress()),
                    'pm10' => strval($device->getTemperature()),
                    'pm25' => strval($device->getHumidity())
                );
            }

            return $this->json(
                $data,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
        return $this->json([false]);
    }
}