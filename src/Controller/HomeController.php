<?php
namespace App\Controller;

use App\Repository\DeviceRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    #[Route('/', name:'app_index')]
    public function index(DeviceRepository $deviceRepository) {
        $user = $this->getUser();

        $devices = $deviceRepository->findAll();

        $devices = array_filter($devices, function($d) {
            return $d->getTemperature() !== null && $d->getHumidity() !== null;
        });

        $groupedDevices = array_reduce($devices, function($result, $d) {
            if (!isset($result[$d->getAdress()])) {
                $result[$d->getAdress()] = [
                    'adress' => $d->getAdress(),
                    'temperature' => $d->getTemperature(),
                    'humidity' => $d->getHumidity(),
                ];
            }
            return $result;
        }, []);

        $averages = array_map(function($d) use ($devices) {
            $filteredDevices = array_filter($devices, function($device) use ($d) {
                return $device->getAdress() === $d['adress'];
            });

            $temperatures = array_map(function($device) {
                return $device->getTemperature();
            }, $filteredDevices);

            $humidities = array_map(function($device) {
                return $device->getHumidity();
            }, $filteredDevices);

            $averageTemperature = array_sum($temperatures) / count($temperatures);
            $averageHumidity = array_sum($humidities) / count($humidities);

            return [
                'adress' => $d['adress'],
                'temperature' => $averageTemperature,
                'humidity' => $averageHumidity,
            ];
        }, $groupedDevices);

        $groupedDevices = array_values($groupedDevices);


        return $this->render('index.html.twig',
        [
            'items' => $averages,
        ]);
    }
}