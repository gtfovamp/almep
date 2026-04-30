import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    const { results } = await db.prepare(
      'SELECT * FROM subcategories WHERE id = ?'
    ).bind(params.id).all();

    const item = results?.[0];

    if (!item) {
      return errorResponse('Subcategory not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get subcategory error:', error);
    return errorResponse('Failed to fetch subcategory', 500);
  }
};

export const PUT: APIRoute = async ({ params, request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();

    const name = formData.get('name');
    const name_en = formData.get('name_en');
    const name_az = formData.get('name_az');
    const description = formData.get('description');
    const description_en = formData.get('description_en');
    const description_az = formData.get('description_az');

    if (!name) {
      return errorResponse('Name is required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    await db.prepare(
      `UPDATE subcategories
       SET name = ?, name_en = ?, name_az = ?, description = ?, description_en = ?, description_az = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`
    ).bind(
      name,
      name_en || null,
      name_az || null,
      description || null,
      description_en || null,
      description_az || null,
      params.id
    ).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update subcategory error:', error);
    return errorResponse('Failed to update subcategory', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    await db.prepare('DELETE FROM subcategories WHERE id = ?').bind(params.id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete subcategory error:', error);
    return errorResponse('Failed to delete subcategory', 500);
  }
};
