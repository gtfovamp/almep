import type { APIRoute } from 'astro';
import { getItem, updateItem, deleteItem, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const item = await getItem('certificates', params.id!);

    if (!item) {
      return errorResponse('Certificate not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get certificate error:', error);
    return errorResponse('Failed to fetch certificate', 500);
  }
};

export const PUT: APIRoute = async ({ params, request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();
    const image = formData.get('image') as File | null;

    // Validate image if provided
    if (image && image.size > 0) {
      const imageError = validateImageFile(image);
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

    await updateItem('certificates', params.id!, fields, image && image.size > 0 ? image : undefined);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update certificate error:', error);
    return errorResponse('Failed to update certificate', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    await deleteItem('certificates', params.id!);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete certificate error:', error);
    return errorResponse('Failed to delete certificate', 500);
  }
};
