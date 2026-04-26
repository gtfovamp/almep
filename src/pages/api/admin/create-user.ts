import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { hashPassword } from '../../../lib/password';
import { checkAuth } from '../../../lib/auth';
import { validateUsername, validatePassword, errorResponse, successResponse, checkRateLimit } from '../../../lib/api-helpers';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    const db = env.DB;

    // Check if any users exist
    const { results: users } = await db.prepare('SELECT COUNT(*) as count FROM admin_users').all();
    const userCount = (users[0] as any)?.count || 0;

    // SECURITY: Only authenticated admins can create users (unless no users exist)
    if (userCount > 0) {
      const isAuthenticated = await checkAuth(cookies);
      if (!isAuthenticated) {
        return errorResponse('Unauthorized', 401);
      }
    }

    // Rate limiting: 5 requests per hour per IP
    const clientIP = request.headers.get('cf-connecting-ip') || 'unknown';
    if (!checkRateLimit(`create-user:${clientIP}`, 5, 60 * 60 * 1000)) {
      return errorResponse('Too many requests. Try again later.', 429);
    }

    const { username, password } = await request.json();

    // Validate input
    if (!username || !password) {
      return errorResponse('Username and password required');
    }

    const usernameError = validateUsername(username);
    if (usernameError) {
      return errorResponse(usernameError);
    }

    const passwordError = validatePassword(password);
    if (passwordError) {
      return errorResponse(passwordError);
    }

    // Check if username already exists
    const existing = await db.prepare(
      'SELECT id FROM admin_users WHERE username = ?'
    ).bind(username).first();

    if (existing) {
      return errorResponse('Username already exists');
    }

    // Hash password
    const passwordHash = await hashPassword(password);

    // Create user
    await db.prepare(
      'INSERT INTO admin_users (username, password_hash) VALUES (?, ?)'
    ).bind(username, passwordHash).run();

    return successResponse({ success: true }, 201);
  } catch (error) {
    console.error('Create user error:', error);
    return errorResponse('Failed to create user', 500);
  }
};
