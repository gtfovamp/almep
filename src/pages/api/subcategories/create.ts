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

    const formData = await request.formData();

    const category_id = formData.get('category_id');
    const name = formData.get('name');
    const name_en = formData.get('name_en');
    const name_az = formData.get('name_az');
    const description = formData.get('description');
    const description_en = formData.get('description_en');
    const description_az = formData.get('description_az');

    if (!category_id || !name) {
      return errorResponse('Category ID and name are required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Get max order_index for this category
    const { results: maxResults } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM subcategories WHERE category_id = ?'
    ).bind(category_id).all();
    const maxOrder = maxResults?.[0]?.max_order || 0;

    // Insert subcategory
    await db.prepare(
      `INSERT INTO subcategories (category_id, name, name_en, name_az, description, description_en, description_az, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
    ).bind(
      category_id,
      name,
      name_en || null,
      name_az || null,
      description || null,
      description_en || null,
      description_az || null,
      maxOrder + 1
    ).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Create subcategory error:', error);
    return errorResponse('Failed to create subcategory', 500);
  }
};
