import type { APIRoute } from 'astro';
import { createItem, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();

    const title = formData.get('title');
    const title_en = formData.get('title_en');
    const title_az = formData.get('title_az');
    const image = formData.get('image') as File;

    if (!title || !image) {
      return errorResponse('Title and image are required');
    }

    await createItem('certificates', {
      title,
      title_en: title_en || null,
      title_az: title_az || null
    }, image);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Create certificate error:', error);
    return errorResponse('Failed to create certificate', 500);
  }
};
