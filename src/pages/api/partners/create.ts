import type { APIRoute } from 'astro';
import { createItem, validateRequired, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();

    const name = formData.get('name') as string;
    const name_en = formData.get('name_en') as string;
    const name_az = formData.get('name_az') as string;
    const description = formData.get('description') as string;
    const description_en = formData.get('description_en') as string;
    const description_az = formData.get('description_az') as string;
    const image = formData.get('image') as File;

    // Validate required fields
    const validationError = validateRequired({ name, image }, ['name', 'image']);
    if (validationError) {
      return errorResponse(validationError);
    }

    // Validate image
    const imageError = validateImageFile(image);
    if (imageError) {
      return errorResponse(imageError);
    }

    // Create item
    const result = await createItem('partners', {
      name,
      name_en,
      name_az,
      description,
      description_en,
      description_az
    }, image);

    return successResponse({ success: true, id: result.id }, 201);
  } catch (error) {
    console.error('Create partner error:', error);
    return errorResponse('Failed to create partner', 500);
  }
};
