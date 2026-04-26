import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  try {
    const db = env.DB;

    if (!db) {
      return new Response(JSON.stringify({ error: 'Database not available' }), {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    const { items } = await request.json();

    if (!Array.isArray(items)) {
      return new Response(JSON.stringify({ error: 'Invalid data format' }), {
        status: 400,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Update order_index for each item
    for (const item of items) {
      await db.prepare(
        'UPDATE portfolio SET order_index = ? WHERE id = ?'
      ).bind(item.order_index, item.id).run();
    }

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error reordering portfolio:', error);
    return new Response(JSON.stringify({ error: 'Failed to reorder portfolio items' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
