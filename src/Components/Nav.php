<?php
namespace App\Components;

use App\Entity\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('Nav')]
class Nav
{
    public user $user;
}