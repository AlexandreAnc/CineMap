import { McpServer } from '@modelcontextprotocol/sdk/server/mcp.js';
import { createMcpExpressApp } from '@modelcontextprotocol/sdk/server/express.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import { StreamableHTTPServerTransport } from '@modelcontextprotocol/sdk/server/streamableHttp.js';
import { isInitializeRequest } from '@modelcontextprotocol/sdk/types.js';
import { randomUUID } from 'node:crypto';
import { z } from 'zod';

const baseUrl = (process.env.CINEMAP_MCP_BASE_URL || 'http://127.0.0.1:8000').replace(/\/$/, '');
const token = process.env.CINEMAP_MCP_READ_TOKEN;
const transportMode = (process.env.CINEMAP_MCP_TRANSPORT || 'stdio').toLowerCase();
const host = process.env.CINEMAP_MCP_HTTP_HOST || '127.0.0.1';
const port = Number(process.env.CINEMAP_MCP_HTTP_PORT || 3333);
const endpointPath = process.env.CINEMAP_MCP_HTTP_PATH || '/mcp';
const endpointToken = process.env.CINEMAP_MCP_ENDPOINT_TOKEN || '';

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

function createServer() {
    const server = new McpServer({
        name: 'cinemap',
        version: '1.1.0',
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

    return server;
}

async function startStdio() {
    const server = createServer();
    const transport = new StdioServerTransport();
    await server.connect(transport);
    console.error('[cinemap-mcp] Transport stdio prêt.');
}

function isAuthorized(req) {
    if (endpointToken === '') {
        return true;
    }
    const authHeader = req.headers.authorization;
    if (typeof authHeader !== 'string' || !authHeader.startsWith('Bearer ')) {
        return false;
    }
    const givenToken = authHeader.slice('Bearer '.length).trim();
    return givenToken === endpointToken;
}

async function startHttp() {
    const app = createMcpExpressApp({ host });
    const transports = new Map();

    app.all(endpointPath, async (req, res) => {
        if (!isAuthorized(req)) {
            res.status(401).json({
                jsonrpc: '2.0',
                error: { code: -32001, message: 'Unauthorized' },
                id: null,
            });
            return;
        }

        try {
            const sessionId = req.headers['mcp-session-id'];
            let transport;

            if (typeof sessionId === 'string' && transports.has(sessionId)) {
                transport = transports.get(sessionId);
            } else if (!sessionId && req.method === 'POST' && isInitializeRequest(req.body)) {
                transport = new StreamableHTTPServerTransport({
                    sessionIdGenerator: () => randomUUID(),
                    onsessioninitialized: (newSessionId) => {
                        transports.set(newSessionId, transport);
                    },
                });

                transport.onclose = () => {
                    if (transport.sessionId) {
                        transports.delete(transport.sessionId);
                    }
                };

                const server = createServer();
                await server.connect(transport);
            } else {
                res.status(400).json({
                    jsonrpc: '2.0',
                    error: { code: -32000, message: 'Bad Request: No valid session ID provided' },
                    id: null,
                });
                return;
            }

            await transport.handleRequest(req, res, req.body);
        } catch (error) {
            console.error('[cinemap-mcp] Erreur HTTP:', error);
            if (!res.headersSent) {
                res.status(500).json({
                    jsonrpc: '2.0',
                    error: { code: -32603, message: 'Internal server error' },
                    id: null,
                });
            }
        }
    });

    app.get('/healthz', (req, res) => {
        res.status(200).json({ ok: true, transport: 'http' });
    });

    app.listen(port, host, (error) => {
        if (error) {
            console.error('[cinemap-mcp] Échec de démarrage HTTP:', error);
            process.exit(1);
        }
        console.error(`[cinemap-mcp] Transport HTTP prêt: http://${host}:${port}${endpointPath}`);
    });
}

if (transportMode === 'http') {
    await startHttp();
} else if (transportMode === 'stdio') {
    await startStdio();
} else {
    console.error("Valeur invalide pour CINEMAP_MCP_TRANSPORT. Utilise 'stdio' ou 'http'.");
    process.exit(1);
}
