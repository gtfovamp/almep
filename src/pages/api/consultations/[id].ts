import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    await db.prepare('DELETE FROM consultations WHERE id = ?').bind(params.id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete consultation error:', error);
    return errorResponse('Failed to delete consultation', 500);
  }
};
