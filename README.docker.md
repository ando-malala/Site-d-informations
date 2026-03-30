# Lancement Docker

## Versions utilisées
- Apache + PHP : `php:8.2-apache`
- MySQL : `mysql:8.0.36`

## Démarrage
```bash
docker compose up -d --build
```

## Accès
- Application : http://localhost:8080
- MySQL : `localhost:3307`

## Credentials MySQL
- Database: `site_info`
- User: `app`
- Password: `app`
- Root password: `root`

## Initialisation base de données
Les scripts SQL sont chargés automatiquement au premier démarrage via:
- `backoffice/bdd/conception.sql`
- `backoffice/bdd/data.sql`

## Arrêt
```bash
docker compose down
```

## Réinitialiser complètement la base
```bash
docker compose down -v
docker compose up -d --build
```
