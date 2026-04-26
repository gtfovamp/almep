import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const db = env.DB;

    if (!db) {
      return new Response(JSON.stringify({ error: 'Database not available' }), {
        status: 503,
        headers: {
          'Content-Type': 'application/json'
        }
      });
    }

    const { results } = await db.prepare(
      'SELECT * FROM portfolio ORDER BY order_index ASC'
    ).all();

    return new Response(JSON.stringify(results || []), {
      status: 200,
      headers: {
        'Content-Type': 'application/json'
      }
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: 'Failed to fetch portfolio' }), {
      status: 500,
      headers: {
        'Content-Type': 'application/json'
      }
    });
  }
};
