<?php

namespace App\Controller;

use App\Entity\Device;
use App\Form\DeviceType;
use App\Repository\DeviceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/device')]
class DeviceController extends AbstractController
{
    #[Route('/', name: 'app_device_index', methods: ['GET'])]
    public function index(DeviceRepository $deviceRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN')) {
            return $this->render('device/index.html.twig', [
                'devices' => $deviceRepository->findAll(),
            ]);
        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/new', name: 'app_device_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DeviceRepository $deviceRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_USER')) {
            $randomKey = bin2hex(random_bytes(10));
            $existingDeviceWithKey = $deviceRepository->findOneBy(['device' => $randomKey]);

            while ($existingDeviceWithKey !== null) {
                $randomKey = bin2hex(random_bytes(10));
                $existingDeviceWithKey = $deviceRepository->findOneBy(['device' => $randomKey]);
            }

            $device = new Device();
            $device->setDevice($randomKey);
            $form = $this->createForm(DeviceType::class, $device);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $device->setUser($this->getUser());

                $deviceRepository->save($device, true);

                return $this->redirectToRoute('app_device_edit', ['id' => $device->getId()], Response::HTTP_SEE_OTHER);
            }

            $form = $this->createForm(DeviceType::class, $device);
            return $this->render('device/new.html.twig', [
                'device' => $device,
                'form' => $form,
                'user' => $this->getUser()
            ]);
        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);

    }

    #[Route('/{id}/edit', name: 'app_device_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Device $device, DeviceRepository $deviceRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN') || $device->getUser() === $this->getUser()) {
            $form = $this->createForm(DeviceType::class, $device);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $deviceRepository->save($device, true);

            }

            return $this->render('device/edit.html.twig', [
                'device' => $device,
                'form' => $form,
                'user' => $this->getUser()
            ]);
        }
        return $this->redirectToRoute('app_index', [], Response::HTTP_SEE_OTHER);


    }

    #[Route('/{id}/delete', name: 'app_device_remove')]
    public function remove (Request $request, Device $device)
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN') || $device->getUser() === $this->getUser()) {

            return $this->render('device/remove.html.twig', [
                'device' => $device,
                'user' => $this->getUser()
            ]);

        }
        return $this->redirectToRoute('app_user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_device_delete', methods: ['POST'])]
    public function delete(Request $request, Device $device, DeviceRepository $deviceRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('ROLE_ADMIN') || $device->getUser() === $this->getUser()) {

            if ($this->isCsrfTokenValid('delete' . $device->getId(), $request->request->get('_token'))) {
                $deviceRepository->remove($device, true);
            }

            return $this->redirectToRoute('app_user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_user_show', ['id' => $this->getUser()->getId()], Response::HTTP_SEE_OTHER);

    }

    #[Route('/add-data/{id}/{temp}/{hum}', name: 'app_device_insertdata', methods: ['GET', 'POST'])]
    public function addData(Request $request, DeviceRepository $deviceRepository): Response
    {
        $id = $request->attributes->get('id');
        $temp = $request->attributes->get('temp');
        $hum = $request->attributes->get('hum');

        if ($deviceRepository->findOneBy(['device' => $id])) {
            $device = $deviceRepository->findOneBy(['device' => $id]);
            $device->setTemperature($temp);
            $device->setHumidity($hum);
            $deviceRepository->save($device, true);
            return $this->json(['status' => 'succes']);
        } else {
            return $this->json(['status' => 'fail', "data" => ["title" => "No device found for this ID"]]);
        }
    }
}