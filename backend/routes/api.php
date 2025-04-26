<?php
use Illuminate\Support\Facades\Route;

// Routes publiques
require __DIR__.'/api_public.php';

// Routes d'authentification
require __DIR__.'/api_auth.php';

// Routes sécurisées extension
require __DIR__.'/api_main_extension.php';

// Routes Focus (sessions focus)
require __DIR__.'/api_focus.php';

// Routes User Website (sites web de l'utilisateur)
require __DIR__.'/api_user_website.php';
