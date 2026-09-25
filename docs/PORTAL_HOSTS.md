# Portails www / entreprises (dev local)

Une seule app Laravel écoute plusieurs hostnames.

## Variables

| Variable | Rôle | Exemple local | Exemple prod |
|----------|------|---------------|--------------|
| `APP_URL` | URL « canonique » (www) | `http://127.0.0.1:8000` | `https://www.talentsdumaroc.com` |
| `HOST_WWW` | Hôte site talents | `127.0.0.1` | `www.talentsdumaroc.com` |
| `HOST_COMPANY` | Hôte portail entreprises | `entreprises.localhost` | `entreprises.talentsdumaroc.com` |
| `SESSION_DOMAIN` | Cookie partagé (prod) | `null` | `.talentsdumaroc.com` |

## URLs locales

Avec `composer dev` (serveur sur `127.0.0.1:8000`) :

- Site talents : http://127.0.0.1:8000/
- Portail entreprises : http://entreprises.localhost:8000/
- Ancienne URL : http://127.0.0.1:8000/entreprises → redirection 301 vers le portail

`entreprises.localhost` résout en général vers `127.0.0.1` sans fichier hosts (Windows / macOS / Linux récents). Sinon, ajouter dans le fichier hosts :

```
127.0.0.1 entreprises.localhost
```

## Production (Hostinger)

1. DNS : enregistrement `A` / `CNAME` pour `entreprises` vers le même serveur que www.
2. Alias / domaine additionnel pointant vers le même `public/` Laravel.
3. SSL couvrant `entreprises.talentsdumaroc.com`.
4. `.env` : `HOST_*`, `SESSION_DOMAIN=.talentsdumaroc.com`, `SESSION_SECURE_COOKIE=true`.
5. `HOST_WWW` = hôte canonique talents (ex. `talentsdumaroc.com`). Le jumeau `www.` (ou l’apex si `HOST_WWW` est en `www.`) est redirigé en **301** vers cet hôte.
