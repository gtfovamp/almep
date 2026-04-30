import type { APIRoute } from 'astro';
import { getItem, updateItem, deleteItem, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const item = await getItem('news', params.id!);

    if (!item) {
      return errorResponse('News item not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get news error:', error);
    return errorResponse('Failed to fetch news item', 500);
  }
};

export const PUT: APIRoute = async ({ params, request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();
    const cover_image = formData.get('cover_image') as File | null;

    // Validate image if provided
    if (cover_image && cover_image.size > 0) {
      const imageError = validateImageFile(cover_image);
      if (imageError) {
        return errorResponse(imageError);
      }
    }

    const fields: Record<string, any> = {};

    const title = formData.get('title');
    if (title) fields.title = title;

    const title_en = formData.get('title_en');
    if (title_en !== null) fields.title_en = title_en;

    const title_az = formData.get('title_az');
    if (title_az !== null) fields.title_az = title_az;

    const blocks = formData.get('blocks');
    if (blocks) fields.blocks = blocks;

    const published_at = formData.get('published_at');
    if (published_at) fields.published_at = published_at;

    await updateItem('news', params.id!, fields, cover_image && cover_image.size > 0 ? cover_image : undefined, 'cover_image_url');

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update news error:', error);
    return errorResponse('Failed to update news item', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    await deleteItem('news', params.id!);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete news error:', error);
    return errorResponse('Failed to delete news item', 500);
  }
};
