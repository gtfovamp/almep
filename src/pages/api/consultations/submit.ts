import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  try {
    const formData = await request.formData();

    const name = formData.get('name');
    const email = formData.get('email');
    const phone = formData.get('phone');

    if (!name || !email || !phone) {
      return errorResponse('All fields are required');
    }

    // Basic validation
    if (typeof name !== 'string' || name.length < 2 || name.length > 200) {
      return errorResponse('Name must be between 2 and 200 characters');
    }

    if (typeof email !== 'string' || !email.includes('@')) {
      return errorResponse('Invalid email address');
    }

    if (typeof phone !== 'string' || phone.length < 5 || phone.length > 50) {
      return errorResponse('Invalid phone number');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Insert consultation request
    await db.prepare(
      `INSERT INTO consultations (name, email, phone)
       VALUES (?, ?, ?)`
    ).bind(name, email, phone).run();

    return successResponse({ success: true, message: 'Thank you! We will contact you soon.' });
  } catch (error) {
    console.error('Submit consultation error:', error);
    return errorResponse('Failed to submit consultation request', 500);
  }
};
