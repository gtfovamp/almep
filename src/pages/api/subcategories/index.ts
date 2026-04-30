import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    const { results } = await db.prepare(
      'SELECT * FROM subcategories ORDER BY category_id ASC, order_index ASC'
    ).all();

    return successResponse(results || []);
  } catch (error) {
    console.error('Get subcategories error:', error);
    return errorResponse('Failed to fetch subcategories', 500);
  }
};
