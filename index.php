<?php
header('Content-Type: text/html; charset=UTF-8');

// Fonctions pour récupérer les stats du VPS
function getUptime() {
    $uptime = shell_exec("uptime -p");
    if ($uptime) {
        $uptime = str_replace(["up", "days", "day", "hours", "hour", "minutes", "minute"], ["En ligne depuis", "jours", "jour", "heures", "heure", "minutes", "minute"], $uptime);
        return $uptime;
    }
    return "Indisponible";
}

function getCPU() {
    $load = sys_getloadavg();
    return round($load[0], 2);
}

function getRAM() {
    $free = shell_exec("free -m");
    if ($free) {
        $lines = explode("\n", trim($free));
        $parts = preg_split('/\s+/', $lines[1]);
        return [$parts[2], $parts[1]]; // utilisé, total
    }
    return [0, 0];
}

function getDisk() {
    return round(disk_free_space("/") / 1024 / 1024 / 1024, 2); // Espace disque libre en GB
}

// Récupération des stats
$cpu = getCPU();
[$ram_used, $ram_total] = getRAM();
$disk = getDisk();
$uptime = getUptime();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🖥️ AzureLab 🖥️</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQuery -->
    <style>
        body { font-family: Arial, sans-serif; background: #121212; color: white; margin: 0; padding: 0; }
        .container { width: 90%; max-width: 1200px; margin: auto; padding-top: 20px; }
        h1 { text-align: center; color: #8a2be2; animation: fadeIn 1s ease-in-out; }
        .dashboard { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; margin-top: 20px; animation: fadeIn 2s ease-in-out; }
        .box { background: #1e1e1e; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 0 10px rgba(0,0,0,0.2); flex-basis: calc(50% - 40px); min-width: 200px; max-width: 300px; flex-grow: 1; animation: slideIn 1s ease-out; }
        .chart-container { width: 100%; height: 150px; position: relative; display: flex; justify-content: center; align-items: center; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px; }
        .error { color: red; font-weight: bold; }

        @media (max-width: 768px) {
            .box { flex-basis: calc(100% - 40px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
    <script>
        // Fonction pour initialiser les graphiques avec les données
        function initCharts(cpu, ram_used, ram_total, disk) {
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

        // Fonction pour mettre à jour les stats via AJAX
        function updateStats() {
            $.get(window.location.href, function(data) {
                // Extraire les nouvelles données du serveur
                let newCPU = $(data).find("#cpu").text();
                let newRAM = $(data).find("#ram").text();
                let newDisk = $(data).find("#disk").text();
                let newUptime = $(data).find("#uptime").text();

                // Mettre à jour les éléments de la page
                $('#cpu').text(newCPU);
                $('#ram').text(newRAM);
                $('#disk').text(newDisk);
                $('#uptime').text(newUptime);

                // Recréer les graphiques avec les nouvelles données
                initCharts(newCPU.replace('%', ''), newRAM.split(' ')[0], 100, newDisk.replace(' GB', ''));
            });
        }

        // Rafraîchir les données toutes les 5 secondes
        $(document).ready(function() {
            setInterval(updateStats, 5000); // Met à jour toutes les 5 secondes
            initCharts(<?= $cpu ?>, <?= $ram_used ?>, <?= $ram_total ?>, <?= $disk ?>); // Initialisation au démarrage
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
            <p id="disk"><?= "$disk GB" ?> libre</p>
        </div>
        <div class="box" id="cpuChartContainer">
            <h2>Utilisation CPU 🧑‍💻</h2>
            <div class="chart-container"><canvas id="cpuChart"></canvas></div>
            <p id="cpu"><?= "$cpu%" ?> utilisé</p>
        </div>
        <div class="box" id="ramChartContainer">
            <h2>Utilisation RAM 🧠</h2>
            <div class="chart-container"><canvas id="ramChart"></canvas></div>
            <p id="ram"><?= "$ram_used MB / $ram_total MB" ?> utilisé</p>
        </div>
        <div class="box" id="uptime">
            <h2>Uptime ⏳</h2>
            <p><?= $uptime ?></p>
        </div>
    </div>
</div>

</body>
</html>
