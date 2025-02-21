# AzureLab Dashboard

AzureLab est un tableau de bord en temps réel pour surveiller les statistiques d'un serveur VPS. Il affiche des informations telles que l'utilisation du CPU, de la RAM, de l'espace disque et l'uptime. Les graphiques sont mis à jour automatiquement toutes les secondes pour refléter les dernières informations disponibles, tout en respectant un délai de mise à jour pour éviter de surcharger le serveur.

## Fonctionnalités

- **Affichage en temps réel** : Suivi continu de l'utilisation du CPU, de la RAM, de l'espace disque et de l'uptime.
- **Graphiques interactifs** : Utilisation de graphiques "doughnut" pour une visualisation claire et esthétique des données.
- **Mise à jour automatique** : Les données sont mises à jour toutes les secondes sans recharger la page.
- **Animation** : Des animations CSS rendent le tableau de bord dynamique et agréable à utiliser.

## Prérequis

Avant de déployer AzureLab, assurez-vous que votre serveur répond aux conditions suivantes :

- PHP 7.0+ (ou supérieur) avec `shell_exec()` activé.
- Accès SSH pour exécuter des commandes système (`uptime`, `free`, `disk_free_space`, etc.).
- Un serveur web avec prise en charge de PHP (ex. Apache, Nginx).
- Internet pour charger les bibliothèques externes (Chart.js, jQuery).

## Installation

1. Clonez ce repository sur votre serveur :

   ```bash
   git clone https://github.com/azuryonoff/AzureLab-Dashboard.git
   ```

2. Accédez au répertoire du projet :

   ```bash
   cd azurelab-dashboard
   ```

3. Déployez le fichier `index.php` sur votre serveur Web. Par exemple, dans le répertoire `public_html` ou `www`.

4. Ouvrez votre navigateur et accédez à votre serveur. Par exemple :

   ```
   http://votre-serveur/azurelab-dashboard/index.php
   ```

## Fonctionnement

1. **Disque libre 💾** : Affiche l'espace disque libre sur votre serveur.
2. **Utilisation CPU 🧑‍💻** : Affiche l'utilisation actuelle du CPU en pourcentage.
3. **Utilisation RAM 🧠** : Affiche la RAM utilisée et la RAM totale.
4. **Uptime ⏳** : Affiche depuis combien de temps votre serveur est en ligne.

Les graphiques sont générés automatiquement à l'aide de la bibliothèque [Chart.js](https://www.chartjs.org/) et sont mis à jour toutes les secondes.

## Personnalisation

- **Animations CSS** : Vous pouvez modifier les animations dans la section CSS pour adapter le style à vos préférences.
- **Délai de mise à jour** : Vous pouvez ajuster la fréquence de mise à jour dans le fichier JavaScript en modifiant le paramètre du délai dans `setInterval(updateStats, 1000);`.

## Aide

Si vous rencontrez un problème, ouvrez une issue sur GitHub et nous vous aiderons à résoudre le problème.

## Contribuer

1. Fork ce repository.
2. Créez une nouvelle branche (`git checkout -b feature/amélioration`).
3. Effectuez vos modifications.
4. Testez vos modifications localement.
5. Soumettez une Pull Request.


## Remerciements

- [Chart.js](https://www.chartjs.org/) pour la bibliothèque de graphiques.
- [jQuery](https://jquery.com/) pour simplifier l'interaction avec le DOM.
- L'API PHP pour récupérer les données système.

---

**AzureLab Dashboard** - Un projet simple pour surveiller votre VPS de manière interactive et esthétique.
