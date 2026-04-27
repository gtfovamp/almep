import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { verifyPassword } from '../../../lib/password';
import { errorResponse, successResponse, checkRateLimit, cleanupExpiredSessions } from '../../../lib/api-helpers';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    console.log('Login attempt started');

    // Rate limiting: 5 login attempts per 15 minutes per IP
    const clientIP = request.headers.get('cf-connecting-ip') || 'unknown';
    if (!checkRateLimit(`login:${clientIP}`, 5, 15 * 60 * 1000)) {
      console.log('Rate limit exceeded for IP:', clientIP);
      return errorResponse('Too many login attempts. Try again later.', 429);
    }

    const db = env.DB;
    console.log('DB available:', !!db);

    const { username, password } = await request.json();
    console.log('Login request for username:', username);

    if (!username || !password) {
      return errorResponse('Username and password required');
    }

    // Cleanup expired sessions periodically
    await cleanupExpiredSessions();

    // Find user - use constant-time comparison to prevent timing attacks
    const user = await db.prepare(
      'SELECT * FROM admin_users WHERE username = ?'
    ).bind(username).first();

    console.log('User found:', !!user);

    // Always verify password even if user doesn't exist (timing attack prevention)
    const dummyHash = 'AAAAAAAAAAAAAAAAAAAAAP8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==';
    const passwordHash = user?.password_hash || dummyHash;

    console.log('Verifying password...');
    const isValid = await verifyPassword(password, passwordHash as string);
    console.log('Password valid:', isValid);

    if (!user || !isValid) {
      // Generic error message to prevent username enumeration
      return errorResponse('Invalid credentials', 401);
    }

    // Generate secure session token
    const token = crypto.randomUUID();
    const expiresAt = new Date(Date.now() + 7 * 24 * 60 * 60 * 1000); // 7 days

    console.log('Creating session...');
    // Create session
    await db.prepare(
      'INSERT INTO admin_sessions (user_id, token, expires_at) VALUES (?, ?, ?)'
    ).bind(user.id, token, expiresAt.toISOString()).run();

    // Set secure cookie
    cookies.set('admin_token', token, {
      path: '/',
      httpOnly: true,
      secure: true,
      sameSite: 'strict',
      expires: expiresAt
    });

    console.log('Login successful');
    return successResponse({ success: true });
  } catch (error) {
    console.error('Login error:', error);
    console.error('Error stack:', error instanceof Error ? error.stack : 'No stack');
    return errorResponse('Authentication failed', 500);
  }
};
