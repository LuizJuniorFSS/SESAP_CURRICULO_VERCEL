<?php
// Wrapper para executar a página original no Vercel
chdir(dirname(__DIR__, 3));
require 'pages/processar.php';