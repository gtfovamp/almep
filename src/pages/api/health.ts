import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const checks = {
      db: false,
      kv: false,
      session: false,
      timestamp: new Date().toISOString()
    };

    // Check DB
    try {
      if (env.DB) {
        await env.DB.prepare('SELECT 1').first();
        checks.db = true;
      }
    } catch (e) {
      checks.db = `Error: ${e}`;
    }

    // Check KV
    try {
      if (env.KV) {
        await env.KV.get('test');
        checks.kv = true;
      }
    } catch (e) {
      checks.kv = `Error: ${e}`;
    }

    // Check SESSION
    try {
      if (env.SESSION) {
        await env.SESSION.get('test');
        checks.session = true;
      }
    } catch (e) {
      checks.session = `Error: ${e}`;
    }

    return new Response(JSON.stringify(checks, null, 2), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: String(error) }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
