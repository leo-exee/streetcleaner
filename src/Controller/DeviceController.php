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
        return $this->render('device/index.html.twig', [
            'devices' => $deviceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_device_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DeviceRepository $deviceRepository): Response
    {
        $device = new Device();
        $form = $this->createForm(DeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deviceRepository->save($device, true);

            return $this->redirectToRoute('app_device_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('device/new.html.twig', [
            'device' => $device,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_device_show', methods: ['GET'])]
    public function show(Device $device): Response
    {
        return $this->render('device/show.html.twig', [
            'device' => $device,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_device_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Device $device, DeviceRepository $deviceRepository): Response
    {
        $form = $this->createForm(DeviceType::class, $device);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deviceRepository->save($device, true);

            return $this->redirectToRoute('app_device_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('device/edit.html.twig', [
            'device' => $device,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_device_delete', methods: ['POST'])]
    public function delete(Request $request, Device $device, DeviceRepository $deviceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$device->getId(), $request->request->get('_token'))) {
            $deviceRepository->remove($device, true);
        }

        return $this->redirectToRoute('app_device_index', [], Response::HTTP_SEE_OTHER);
    }
}
