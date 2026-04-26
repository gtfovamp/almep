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

    const title = formData.get('title') as string;
    const title_en = formData.get('title_en') as string;
    const title_az = formData.get('title_az') as string;
    const year = formData.get('year') as string;
    const description = formData.get('description') as string;
    const description_en = formData.get('description_en') as string;
    const description_az = formData.get('description_az') as string;
    const image = formData.get('image') as File;

    // Validate required fields
    const validationError = validateRequired({ title, year, image }, ['title', 'year', 'image']);
    if (validationError) {
      return errorResponse(validationError);
    }

    // Validate image
    const imageError = validateImageFile(image);
    if (imageError) {
      return errorResponse(imageError);
    }

    // Create item
    const result = await createItem('portfolio', {
      title,
      title_en,
      title_az,
      year,
      description,
      description_en,
      description_az
    }, image);

    return successResponse({ success: true, id: result.id }, 201);
  } catch (error) {
    console.error('Create portfolio error:', error);
    return errorResponse('Failed to create portfolio item', 500);
  }
};
