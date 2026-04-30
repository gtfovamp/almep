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

    const name = formData.get('name');
    const name_en = formData.get('name_en');
    const name_az = formData.get('name_az');
    const description = formData.get('description');
    const description_en = formData.get('description_en');
    const description_az = formData.get('description_az');
    const icon = formData.get('icon') as File | null;

    if (!name) {
      return errorResponse('Name is required');
    }

    await createItem('categories', {
      name,
      name_en: name_en || null,
      name_az: name_az || null,
      description: description || null,
      description_en: description_en || null,
      description_az: description_az || null
    }, icon && icon.size > 0 ? icon : undefined, 'icon_url');

    return successResponse({ success: true });
  } catch (error) {
    console.error('Create category error:', error);
    return errorResponse('Failed to create category', 500);
  }
};
