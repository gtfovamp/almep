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

    const { id } = await request.json();

    if (!id) {
      return errorResponse('ID is required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Get max order_index for approved testimonials
    const { results: maxResults } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM testimonials WHERE approved = 1'
    ).all();
    const maxOrder = maxResults?.[0]?.max_order || 0;

    // Approve testimonial and set order
    await db.prepare(
      'UPDATE testimonials SET approved = 1, order_index = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
    ).bind(maxOrder + 1, id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Approve testimonial error:', error);
    return errorResponse('Failed to approve testimonial', 500);
  }
};
