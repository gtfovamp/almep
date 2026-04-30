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
    const blocks = formData.get('blocks') as string;
    const published_at = formData.get('published_at') as string;
    const cover_image = formData.get('cover_image') as File;

    // Validate required fields
    const validationError = validateRequired({ title, blocks, published_at, cover_image }, ['title', 'blocks', 'published_at', 'cover_image']);
    if (validationError) {
      return errorResponse(validationError);
    }

    // Validate cover image
    const imageError = validateImageFile(cover_image);
    if (imageError) {
      return errorResponse(imageError);
    }

    // Create item
    const result = await createItem('blog', {
      title,
      title_en,
      title_az,
      blocks,
      published_at
    }, cover_image, 'cover_image_url');

    return successResponse({ success: true, id: result.id }, 201);
  } catch (error) {
    console.error('Create blog error:', error);
    return errorResponse('Failed to create blog item', 500);
  }
};
