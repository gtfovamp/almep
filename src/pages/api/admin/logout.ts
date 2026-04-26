import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const POST: APIRoute = async ({ cookies }) => {
  try {
    const db = env.DB;
    const token = cookies.get('admin_token')?.value;

    if (token) {
      // Delete session from database
      await db.prepare('DELETE FROM admin_sessions WHERE token = ?').bind(token).run();
    }

    // Clear cookie
    cookies.delete('admin_token', { path: '/' });

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Logout error:', error);
    return new Response(JSON.stringify({ error: 'Internal server error' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
