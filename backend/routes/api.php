<?php
use Illuminate\Support\Facades\Route;

// Routes publiques
require __DIR__ . '/common/api_public.php';

// Routes d'authentification
require __DIR__ . '/common/api_auth.php';

// Routes sécurisées extension
require __DIR__ . '/extension/api_main_extension.php';

// Routes Focus (sessions focus)
require __DIR__ . '/extension/api_focus.php';

// Routes User Website (sites web de l'utilisateur)
require __DIR__ . '/extension/api_user_website.php';

// Routes Arises Ai
require __DIR__ . '/saas/api_arises_ai.php';

// Routes du calendrier
require __DIR__ . '/saas/api_calendar.php';
