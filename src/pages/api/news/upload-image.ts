import type { APIRoute } from 'astro';
import { validateImageFile, uploadImage, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();
    const image = formData.get('image') as File;

    if (!image) {
      return errorResponse('Image is required');
    }

    // Validate image
    const imageError = validateImageFile(image);
    if (imageError) {
      return errorResponse(imageError);
    }

    // Upload image
    const imageUrl = await uploadImage(image, 'news/content');

    return successResponse({ url: imageUrl });
  } catch (error) {
    console.error('Upload image error:', error);
    return errorResponse('Failed to upload image', 500);
  }
};
