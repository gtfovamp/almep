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
      'SELECT * FROM categories ORDER BY order_index ASC'
    ).all();

    return successResponse(results || []);
  } catch (error) {
    console.error('Get categories error:', error);
    return errorResponse('Failed to fetch categories', 500);
  }
};
