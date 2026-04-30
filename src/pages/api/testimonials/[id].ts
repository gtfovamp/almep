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
      'SELECT * FROM testimonials WHERE id = ?'
    ).bind(params.id).all();

    const item = results?.[0];

    if (!item) {
      return errorResponse('Testimonial not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get testimonial error:', error);
    return errorResponse('Failed to fetch testimonial', 500);
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
    const text = formData.get('text');
    const text_en = formData.get('text_en');
    const text_az = formData.get('text_az');

    if (!name || !text) {
      return errorResponse('Name and text are required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    await db.prepare(
      `UPDATE testimonials
       SET name = ?, name_en = ?, name_az = ?, text = ?, text_en = ?, text_az = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`
    ).bind(name, name_en || null, name_az || null, text, text_en || null, text_az || null, params.id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update testimonial error:', error);
    return errorResponse('Failed to update testimonial', 500);
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

    await db.prepare('DELETE FROM testimonials WHERE id = ?').bind(params.id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete testimonial error:', error);
    return errorResponse('Failed to delete testimonial', 500);
  }
};
