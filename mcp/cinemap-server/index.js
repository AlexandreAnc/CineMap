import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { z } from 'zod';

const baseUrl = (process.env.CINEMAP_MCP_BASE_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const token = process.env.CINEMAP_MCP_READ_TOKEN;

if (token == null || token === '') {
    console.error('Définis CINEMAP_MCP_READ_TOKEN (même valeur que MCP_READ_TOKEN dans le .env Laravel).');
    process.exit(1);
}

async function getJson(path) {
    const url = `${baseUrl}${path}`;
    const res = await fetch(url, {
        headers: {
            Accept: 'application/json',
            Authorization: `Bearer ${token}`,
        },
    });
    const text = await res.text();
    if (!res.ok) {
        throw new Error(`Requête ${url} → ${res.status} : ${text}`);
    }
    return text;
}

const server = new McpServer({
    name: 'cinemap',
    version: '1.0.0',
});

server.registerTool(
    'list_films',
    {
        description: 'Liste tous les films (id, titre, année, synopsis). Lecture seule.',
    },
    async () => {
        const text = await getJson('/api/mcp/films');
        return { content: [{ type: 'text', text }] };
    },
);

server.registerTool(
    'get_locations_for_film',
    {
        description: "Retourne le film et la liste d'emplacements d'un film (id, noms, ville, upvotes, …).",
        inputSchema: z.object({
            film_id: z.coerce.number().int().positive().describe("Identifiant numérique du film (clé 'id' dans list_films)."),
        }),
    },
    async ({ film_id: filmId }) => {
        const text = await getJson(`/api/mcp/films/${filmId}/locations`);
        return { content: [{ type: 'text', text }] };
    },
);

const transport = new StdioServerTransport();
await server.connect(transport);
