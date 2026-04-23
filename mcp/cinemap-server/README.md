# CineMap — serveur MCP (lecture seule)

Ce processus parle le **protocole MCP** en deux modes :
- **stdio** (local, pour Cursor/Claude Desktop) ;
- **HTTP streamable** (endpoint URL exposable sur ton site).

Les outils **list_films** et **get_locations_for_film** appellent l’API Laravel en HTTP.

## Prérequis

1. Démarre l’appli Laravel (local ou prod).
2. Dans le **`.env` à la racine du projet Laravel**, définis le même secret des deux côtés :

   ```env
   MCP_READ_TOKEN=une_chaine_secrete_longue
   ```

3. Côté serveur MCP, exporte (ou mets dans un `.env` que tu source) :

   ```bash
   export CINEMAP_MCP_READ_TOKEN=une_chaine_secrete_longue
   export CINEMAP_MCP_BASE_URL=http://127.0.0.1:8000
   ```

4. Installe les dépendances (une fois) :

   ```bash
   cd mcp/cinemap-server
   npm install
   ```

## Mode 1 — stdio (compat Cursor locale)

```bash
cd mcp/cinemap-server
npm run start:stdio
```

Il attend des messages sur stdin (c’est le client Cursor / Claude qui le lance, pas toi en interactif).

## Intégration Cursor (stdio)

Dans **Cursor** → *Settings* → *MCP* → *Add new global MCP server* (ou fichier de config du projet).

**Important (macOS)** : utilise un **chemin absolu complet**. Il doit commencer par `/Users/toncompte/...`  
Un chemin du type `/alaix/Dev/...` (sans `Users`) est **invalide** : Node ne trouve pas le fichier.

Pour copier le bon chemin, dans le terminal, à la racine du dépôt :

```bash
realpath mcp/cinemap-server/index.js
# ou: cd mcp/cinemap-server && pwd
# puis + /index.js
```

Exemple (à adapter à ton compte) :

```json
{
  "mcpServers": {
    "cinemap": {
      "command": "node",
      "args": ["/Users/alaix/Dev/MDS/Laravel/my-app/mcp/cinemap-server/index.js"],
      "env": {
        "CINEMAP_MCP_BASE_URL": "http://127.0.0.1:8000",
        "CINEMAP_MCP_READ_TOKEN": "une_chaine_secrete_longue"
      }
    }
  }
}
```

Remplace le `args` par le chemin donné par `realpath` sur **ta** machine, et mets le **même** secret que `MCP_READ_TOKEN` dans le `.env` Laravel. Garde `php artisan serve` lancé en parallèle.

## Mode 2 — HTTP (endpoint MCP via URL)

Ce mode expose un endpoint MCP HTTP (par défaut `GET/POST/DELETE /mcp`).

Variables utiles :

```bash
export CINEMAP_MCP_TRANSPORT=http
export CINEMAP_MCP_HTTP_HOST=127.0.0.1
export CINEMAP_MCP_HTTP_PORT=3333
export CINEMAP_MCP_HTTP_PATH=/mcp
# optionnel mais recommandé pour protéger l'endpoint MCP lui-même :
export CINEMAP_MCP_ENDPOINT_TOKEN=un_autre_secret
```

Démarrage :

```bash
cd mcp/cinemap-server
npm run start:http
```

Endpoint local :

```text
http://127.0.0.1:3333/mcp
```

Si tu veux le publier sur ton domaine (`cinemap.aanc.fr`) :
- reverse proxy Nginx/Caddy vers `127.0.0.1:3333`;
- exemple d’URL finale : `https://cinemap.aanc.fr/mcp`;
- garde l’auth (`CINEMAP_MCP_ENDPOINT_TOKEN`) et le HTTPS.

Quand `CINEMAP_MCP_ENDPOINT_TOKEN` est défini, les clients doivent envoyer :

```text
Authorization: Bearer <CINEMAP_MCP_ENDPOINT_TOKEN>
```

## Appels HTTP directs vers Laravel (sans MCP)

Avec le même token :

```bash
curl -s -H "Authorization: Bearer TON_TOKEN" \
  http://127.0.0.1:8000/api/mcp/films

curl -s -H "Authorization: Bearer TON_TOKEN" \
  http://127.0.0.1:8000/api/mcp/films/1/locations
```

## Outils MCP exposés

| Outil | Rôle |
|--------|------|
| `list_films` | Liste des films (JSON) |
| `get_locations_for_film` | Paramètre : `film_id` — emplacements du film |

Aucune écriture : lecture seule.
