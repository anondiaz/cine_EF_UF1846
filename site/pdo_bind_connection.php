<?php

// Seleccionaremos la conexión en función de la ubicación
require_once 'connection1.php'; // IP CIEF
// require_once 'connection2.php'; // IP CASA 1
// require_once 'connection3.php'; // IP CASA 2
// require_once 'connection4.php'; // byet 

// print_R($_SERVER);

$ipServidor = getHostByName(getHostName());
echo "IP del servidor: " . $ipServidor;