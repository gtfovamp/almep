import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async ({ url }) => {
  try {
    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    const searchQuery = url.searchParams.get('search');

    if (searchQuery) {
      // Поиск по названию, артикулу или описанию
      const { results } = await db.prepare(
        `SELECT p.*, pi.image_url
         FROM products p
         LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
         WHERE p.name LIKE ? OR p.name_en LIKE ? OR p.name_az LIKE ?
            OR p.article LIKE ?
            OR p.description LIKE ? OR p.description_en LIKE ? OR p.description_az LIKE ?
         ORDER BY p.order_index ASC
         LIMIT 20`
      ).bind(
        `%${searchQuery}%`, `%${searchQuery}%`, `%${searchQuery}%`,
        `%${searchQuery}%`,
        `%${searchQuery}%`, `%${searchQuery}%`, `%${searchQuery}%`
      ).all();

      return successResponse(results || []);
    }

    const { results } = await db.prepare(
      'SELECT * FROM products ORDER BY subcategory_id ASC, order_index ASC'
    ).all();

    return successResponse(results || []);
  } catch (error) {
    console.error('Get products error:', error);
    return errorResponse('Failed to fetch products', 500);
  }
};
