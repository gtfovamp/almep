import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const { items } = await request.json();

    if (!Array.isArray(items)) {
      return errorResponse('Invalid data format');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Update order for each item
    for (const item of items) {
      await db.prepare(
        'UPDATE testimonials SET order_index = ? WHERE id = ?'
      ).bind(item.order, item.id).run();
    }

    return successResponse({ success: true });
  } catch (error) {
    console.error('Reorder testimonials error:', error);
    return errorResponse('Failed to reorder testimonials', 500);
  }
};
