import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  try {
    const formData = await request.formData();

    const name = formData.get('name');
    const text = formData.get('text');

    if (!name || !text) {
      return errorResponse('Name and text are required');
    }

    // Basic validation
    if (typeof name !== 'string' || name.length < 2 || name.length > 200) {
      return errorResponse('Name must be between 2 and 200 characters');
    }

    if (typeof text !== 'string' || text.length < 10 || text.length > 1000) {
      return errorResponse('Text must be between 10 and 1000 characters');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Insert testimonial with approved = 0 (pending)
    await db.prepare(
      `INSERT INTO testimonials (name, text, approved, order_index)
       VALUES (?, ?, 0, 0)`
    ).bind(name, text).run();

    return successResponse({ success: true, message: 'Thank you! Your testimonial has been submitted for review.' });
  } catch (error) {
    console.error('Submit testimonial error:', error);
    return errorResponse('Failed to submit testimonial', 500);
  }
};
