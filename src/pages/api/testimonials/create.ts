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

    // Get max order_index
    const { results: maxResults } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM testimonials'
    ).all();
    const maxOrder = maxResults?.[0]?.max_order || 0;

    // Insert new testimonial
    await db.prepare(
      `INSERT INTO testimonials (name, name_en, name_az, text, text_en, text_az, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?)`
    ).bind(name, name_en || null, name_az || null, text, text_en || null, text_az || null, maxOrder + 1).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Create testimonial error:', error);
    return errorResponse('Failed to create testimonial', 500);
  }
};
