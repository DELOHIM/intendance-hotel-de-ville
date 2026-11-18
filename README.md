# Solution d'intendance — Hôtel de Ville de Kinshasa (v1.1, bâtiment unique)

Application web **Laravel 11** (PHP 8.2+) : bureaux par étage + QR codes, inventaire des biens,
signalement d'incidents, alertes **WhatsApp (Green API)** via les files d'attente Laravel,
tableau de bord, rapports PDF/Excel, rôles et journal d'audit.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
# Éditer .env : DB_*, GREEN_API_ID_INSTANCE, GREEN_API_TOKEN, MAIL_*
php artisan migrate --seed
php artisan storage:link
php artisan serve          # http://localhost:8000
```

Dans un second terminal (envoi WhatsApp asynchrone) :

```bash
php artisan queue:work --tries=4
```

En production, deux options :
- `supervisor` qui maintient `php artisan queue:work`, **ou**
- un cron `* * * * * php /chemin/artisan schedule:run` (le scheduler traite aussi la file et lance la sauvegarde quotidienne à 02:00).

## Comptes de démonstration (à changer immédiatement)

| Rôle | Email | Mot de passe |
|---|---|---|
| Super Administrateur | superadmin@hoteldeville.cd | Admin@2026 |
| Administrateur | admin@hoteldeville.cd | Admin@2026 |
| Agent | agent@hoteldeville.cd | Agent@2026 |
| Agent de maintenance | maintenance@hoteldeville.cd | Maint@2026 |

## Configuration WhatsApp (Green API)

1. Créez une instance sur green-api.com et scannez le QR avec le WhatsApp de l'expéditeur.
2. Renseignez `GREEN_API_ID_INSTANCE` et `GREEN_API_TOKEN` dans `.env` (jamais dans le code).
3. Dans l'application : menu **WhatsApp** → numéros des administrateurs + modèle du message → bouton *Tester l'envoi*.
4. Le menu **Journal des envois** liste chaque envoi (envoyé / en attente / échec), avec retentatives automatiques (30 s, 2 min, 10 min) puis bouton *Renvoyer*.

## Droits par rôle

| Fonction | Super Admin | Admin | Agent | Maintenance |
|---|:-:|:-:|:-:|:-:|
| Utilisateurs, journal d'activité | ✔ | | | |
| Étages, catégories, types, WhatsApp, rapports | ✔ | ✔ | | |
| Bureaux (CRUD, QR, archivage), biens, traitement des incidents | ✔ | ✔ | | |
| Consulter bureaux, signaler un incident, voir ses signalements | ✔ | ✔ | ✔ | |
| Traiter les incidents qui lui sont assignés | | | | ✔ |

## Correspondance avec le cahier des charges

- **BF-AUTH** : `AuthController`, `ProfileController`, `UserController` (rôle stocké dans `users.role`, middleware `role:`).
- **BF-BUR** : `BureauController` — numéro unique non modifiable, QR code (SVG/PNG/PDF + planche d'impression), scan `/b/{numero}`, archivage (soft delete), affectation d'agents.
- **BF-BIEN** : `BienController` — catégories, états, transferts avec historique (`bien_historiques`), export CSV (Excel) / PDF.
- **BF-INC** : `IncidentController` — priorités, statuts, photo, commentaires, assignation, historique par bureau et par bien.
- **BF-NOT** : `WhatsappService` + job `SendWhatsappMessage` (file `database`, retentatives, `notification_logs`, modèle éditable, multi-destinataires).
- **BF-DASH** : `DashboardController` (Chart.js) + `RapportController`.
- **BF-PAR** : `ParametreController`, `WhatsappController`, `ActivityLogController` (audit trail).
- **BNF** : bcrypt, CSRF, validation, throttling du login, interface en français et responsive (Tailwind), sauvegarde quotidienne (`php artisan db:backup`).

## Notes techniques

- Tailwind CSS, Alpine.js et Chart.js sont chargés par CDN (aucune compilation Node requise). Pour une installation hors-ligne, installez-les via npm/Vite.
- Les exports « Excel » sont des fichiers CSV (séparateur `;`, UTF-8 avec BOM) qui s'ouvrent directement dans Excel. Pour du `.xlsx` natif, ajoutez `maatwebsite/excel`.
- Le QR code PNG nécessite l'extension PHP `imagick` ; sinon SVG et PDF sont proposés.
- Forcer HTTPS : en `APP_ENV=production` l'application génère des URLs `https`.
- Pour PostgreSQL : `DB_CONNECTION=pgsql`.
