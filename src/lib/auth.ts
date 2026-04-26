import { env } from 'cloudflare:workers';
import type { AstroCookies } from 'astro';

export async function checkAuth(cookies: AstroCookies): Promise<boolean> {
  try {
    const db = env.DB;
    const token = cookies.get('admin_token')?.value;

    if (!token) {
      return false;
    }

    // Check if session exists and is valid
    const session = await db.prepare(
      `SELECT s.*, u.username
       FROM admin_sessions s
       JOIN admin_users u ON s.user_id = u.id
       WHERE s.token = ? AND s.expires_at > datetime('now')`
    ).bind(token).first();

    if (!session) {
      // Clean up invalid token
      cookies.delete('admin_token', { path: '/' });
      return false;
    }

    return true;
  } catch (error) {
    console.error('Auth check error:', error);
    return false;
  }
}

export async function requireAuth(cookies: AstroCookies): Promise<Response | null> {
  const isAuthenticated = await checkAuth(cookies);

  if (!isAuthenticated) {
    return new Response(null, {
      status: 302,
      headers: {
        Location: '/admin/login'
      }
    });
  }

  return null;
}
