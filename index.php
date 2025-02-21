<?php
// Définition du type de contenu de la réponse HTTP
header('Content-Type: text/html; charset=UTF-8');

// Fonction pour obtenir le temps d'activité du serveur (Uptime)
function getUptime() {
    // Exécution de la commande pour obtenir l'uptime
    $uptime = shell_exec("uptime -p");
    if ($uptime) {
        // Remplacer certains termes pour une meilleure lisibilité
        $uptime = str_replace(["up", "days", "day", "hours", "hour", "minutes", "minute"], 
                             ["En ligne depuis", "jours", "jour", "heures", "heure", "minutes", "minute"], 
                             $uptime);
        return $uptime;
    }
    return "Indisponible"; // Si la commande échoue
}

// Fonction pour obtenir l'utilisation CPU du serveur
function getCPU() {
    // Récupérer la charge moyenne du système
    $load = sys_getloadavg();
    return round($load[0], 4); // Utilisation CPU arrondie à 4 décimales
}

// Fonction pour obtenir l'utilisation de la RAM
function getRAM() {
    // Exécuter la commande 'free -m' pour obtenir les informations sur la RAM
    $free = shell_exec("free -m");
    if ($free) {
        // Séparer les lignes et extraire les informations nécessaires
        $lines = explode("\n", trim($free));
        $parts = preg_split('/\s+/', $lines[1]);
        return [$parts[2], $parts[1]]; // Retourne utilisé et total
    }
    return [0, 0]; // Si l'information n'est pas disponible
}

// Fonction pour obtenir l'espace disque libre
function getDisk() {
    // Calcul de l'espace disque libre en Go
    return round(disk_free_space("/") / 1024 / 1024 / 1024, 2); // Convertir les octets en GB
}

// Fonction pour obtenir la latence (ping)
function getLatency() {
    // Exécuter la commande ping pour obtenir la latence
    $pingResult = shell_exec("ping -c 1 8.8.8.8");
    
    if ($pingResult) {
        // Extraire le temps de latence (en ms) à partir du résultat
        preg_match('/time=(\d+.\d+) ms/', $pingResult, $matches);
        
        if (isset($matches[1])) {
            // Retourner la latence sous forme de nombre flottant
            return (float) $matches[1];
        }
    }
    
    return 0; // Si la latence n'est pas disponible
}

// Récupération des statistiques du serveur
$cpu = getCPU();
[$ram_used, $ram_total] = getRAM();
$disk = getDisk();
$uptime = getUptime();
$latency = getLatency();

// Fonction pour récupérer la bande passante (RX/TX)
function getBandwidth() {
    // Lire les statistiques réseau pour obtenir les octets reçus (RX) et envoyés (TX)
    $rx = file_get_contents("/sys/class/net/ens18/statistics/rx_bytes");
    $tx = file_get_contents("/sys/class/net/ens18/statistics/tx_bytes");
    
    if ($rx && $tx) {
        // Calculer la bande passante en Ko/s
        $rx_speed = round(($rx / 1024), 2); // RX en Ko/s
        $tx_speed = round(($tx / 1024), 2); // TX en Ko/s
        return [$rx_speed, $tx_speed];
    }
    return [0, 0]; // Si l'information n'est pas disponible
}

// Récupérer la bande passante
list($rx_speed, $tx_speed) = getBandwidth();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🖥️ AzureLab 🖥️</title>
    <!-- Importation de Chart.js pour les graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        /* Styles CSS pour la mise en page */
        body { font-family: Arial, sans-serif; background: #121212; color: white; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: auto; padding-top: 20px; }
        h1 { text-align: center; color: #8a2be2; }
        .dashboard { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-top: 20px; }
        .box { background: #1e1e1e; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 0 10px rgba(0,0,0,0.2); flex-basis: calc(50% - 40px); min-width: 200px; max-width: 300px; flex-grow: 1; }
        .chart-container { width: 100%; height: 150px; position: relative; display: flex; justify-content: center; align-items: center; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px; }
    </style>
    <script>
        // Fonction pour initialiser les graphiques avec les données
        function initCharts(cpu, ram_used, ram_total, disk) {
            // Graphique pour l'utilisation CPU
            new Chart(document.getElementById("cpuChart"), {
                type: 'doughnut',
                data: { 
                    labels: ["Libre", "Utilisation CPU"], 
                    datasets: [{
                        data: [100 - cpu, cpu],
                        backgroundColor: ["#444", "#8a2be2"],
                    }]
                },
                options: { responsive: true }
            });

            // Graphique pour l'utilisation RAM
            new Chart(document.getElementById("ramChart"), {
                type: 'doughnut',
                data: {
                    labels: ["Libre", "Utilisé"],
                    datasets: [{
                        data: [ram_total - ram_used, ram_used],
                        backgroundColor: ["#444", "#8a2be2"],
                    }]
                },
                options: { responsive: true }
            });

            // Graphique pour l'espace disque
            new Chart(document.getElementById("diskChart"), {
                type: 'doughnut',
                data: {
                    labels: ["Espace utilisé", "Libre"],
                    datasets: [{
                        data: [100 - disk, disk],
                        backgroundColor: ["#444", "#8a2be2"],
                    }]
                },
                options: { responsive: true }
            });
        }

        $(document).ready(function() {
            // Initialisation des graphiques avec les données PHP
            initCharts(<?= $cpu ?>, <?= $ram_used ?>, <?= $ram_total ?>, <?= $disk ?>);
            
            // Rafraîchir la page toutes les 5 secondes
            setInterval(function() {
                location.reload();
            }, 5000); // Page se recharge toutes les 5 secondes
        });
    </script>
</head>
<body>

<div class="container">
    <h1>🖥️ AzureLab 🖥️</h1>

    <div class="dashboard">
        <div class="box" id="diskChartContainer">
            <h2>Disque libre 💾</h2>
            <div class="chart-container"><canvas id="diskChart"></canvas></div>
            <p><?= "$disk GB libre" ?></p>
        </div>
        <div class="box" id="cpuChartContainer">
            <h2>Utilisation CPU 🧑‍💻</h2>
            <div class="chart-container"><canvas id="cpuChart"></canvas></div>
            <p><?= "$cpu%" ?> utilisé</p>
        </div>
        <div class="box" id="ramChartContainer">
            <h2>Utilisation RAM 🧠</h2>
            <div class="chart-container"><canvas id="ramChart"></canvas></div>
            <p><?= "$ram_used MB / $ram_total MB" ?> utilisé</p>
        </div>
        <div class="box" id="uptime">
            <h2>Uptime ⏳</h2>
            <p><?= $uptime ?></p>
        </div>
        <div class="box" id="bandwidth">
            <h2>Bande passante 🌐</h2>
            <p>Upload: <?= "$rx_speed kbit/s" ?></p>
            <p>Download: <?= "$tx_speed kbit/s" ?></p>
        </div>
        <div class="box" id="latency">
            <h2>Temps de réponse ⏱️</h2>
            <p>Latence: <?= round($latency, 2) ?> ms</p>
        </div>
    </div>
</div>

</body>
</html>
