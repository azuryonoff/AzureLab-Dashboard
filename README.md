# AzureLab Dashboard

AzureLab est un tableau de bord en temps réel pour surveiller les statistiques d'un serveur VPS. Il affiche des informations telles que l'utilisation du CPU, de la RAM, de l'espace disque, l'uptime, la bande passante et la latence. Les graphiques sont mis à jour automatiquement toutes les 5 secondes via un rechargement de page pour refléter les dernières informations disponibles.

## Fonctionnalités

- **Affichage en temps réel** : Suivi continu de l'utilisation du CPU, de la RAM, de l'espace disque, de l'uptime, de la bande passante (Upload/Download) et de la latence.
- **Graphiques interactifs** : Utilisation de graphiques "doughnut" pour une visualisation claire et esthétique des données.
- **Mise à jour automatique** : Rechargement complet de la page toutes les 5 secondes pour des données actualisées.
- **Animations CSS** : Interface moderne avec des transitions fluides.

## Prérequis

Avant de déployer AzureLab, assurez-vous que votre serveur répond aux conditions suivantes :

- PHP 7.0+ (ou supérieur) avec `shell_exec()` activé.
- Accès SSH pour exécuter des commandes système (`uptime`, `free`, `ping`, etc.).
- Un serveur web avec prise en charge de PHP (ex. Apache, Nginx).
- Internet pour charger les bibliothèques externes (Chart.js, jQuery).
- Interface réseau `ens18` (ou modification du code pour votre interface).

## Installation

1. Clonez ce repository sur votre serveur :

   ```bash
   git clone https://github.com/azuryonoff/AzureLab-Dashboard.git
   ```

2. Accédez au répertoire du projet :

   ```bash
   cd azurelab-dashboard
   ```

3. Déployez le fichier `index.php` sur votre serveur Web (ex. `public_html` ou `www`).

4. *(Optionnel)* Si votre interface réseau n'est pas `ens18`, modifiez la fonction `getBandwidth()` dans le code pour utiliser votre interface (ex. `eth0`).

5. Ouvrez votre navigateur et accédez à votre serveur :

   ```
   http://votre-serveur/azurelab-dashboard/index.php
   ```

## Fonctionnement

1. **Disque libre 💾** : Affiche l'espace disque libre en GB (extrait via `disk_free_space()`).
2. **Utilisation CPU 🧑‍💻** : Charge moyenne sur 1 minute (via `sys_getloadavg()`).
3. **Utilisation RAM �** : Mémoire utilisée/total en MB (via `free -m`).
4. **Uptime ⏳** : Temps d'activité formaté (via `uptime -p`).
5. **Bande passante 🌐** : Upload (TX) et Download (RX) en kbit/s (via `/sys/class/net/ens18/statistics`).
6. **Latence ⏱️** : Temps de réponse en ms (via `ping 8.8.8.8`).

Les graphiques sont générés avec [Chart.js](https://www.chartjs.org/) et les données se rafraîchissent toutes les 5 secondes.

## Personnalisation

- **Délai de mise à jour** : Modifiez la valeur `5000` dans `setInterval` (fichier JavaScript) pour ajuster la fréquence (en millisecondes).
- **Cible du ping** : Changez l'adresse IP dans `shell_exec("ping -c 1 8.8.8.8")` pour un autre serveur.
- **Interface réseau** : Ajustez `ens18` dans `getBandwidth()` selon votre configuration.
- **Style CSS** : Personnalisez les couleurs, les animations ou la mise en page dans la section `<style>`.

## Aide

- **Problème de données réseau** : Vérifiez le nom de votre interface réseau dans `/sys/class/net/`.
- **Commandes bloquées** : Assurez-vous que `shell_exec()` n'est pas désactivé dans `php.ini`.
- **Latence manquante** : Autorisez les requêtes ICMP (ping) depuis votre serveur.

## Contribuer

1. Fork ce repository.
2. Créez une nouvelle branche (`git checkout -b feature/amélioration`).
3. Effectuez vos modifications.
4. Testez vos modifications localement.
5. Soumettez une Pull Request.

## Remerciements

- [Chart.js](https://www.chartjs.org/) pour les graphiques interactifs.
- [jQuery](https://jquery.com/) pour simplifier les manipulations DOM.
- API PHP pour l'accès aux données système.

---

**AzureLab Dashboard** - Surveillance complète de votre VPS avec des métriques réseau avancées.
