<?php
namespace App\Components;

use App\Entity\User;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('nav')]
class NavComponent
{
    public user $user;
}