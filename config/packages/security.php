<?php 

// config/packages/security.php
use App\Entity\User;
use Symfony\Config\SecurityConfig;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


return static function (SecurityConfig $security) {
    
    $security->provider('app_user_provider')
        ->entity()
        ->class(User::class)
        ->property('email');


    // Use native password hasher, which auto-selects and migrates the best
    // possible hashing algorithm (currently this is "bcrypt")
    $security->passwordHasher(PasswordAuthenticatedUserInterface::class)
    ->algorithm('auto');
};