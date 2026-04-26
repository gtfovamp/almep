import type { APIRoute } from 'astro';
import { getItem, updateItem, deleteItem, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const item = await getItem('partners', params.id!);

    if (!item) {
      return errorResponse('Partner not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get partner error:', error);
    return errorResponse('Failed to fetch partner', 500);
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

    const name = formData.get('name');
    if (name) fields.name = name;

    const name_en = formData.get('name_en');
    if (name_en !== null) fields.name_en = name_en;

    const name_az = formData.get('name_az');
    if (name_az !== null) fields.name_az = name_az;

    const description = formData.get('description');
    if (description !== null) fields.description = description;

    const description_en = formData.get('description_en');
    if (description_en !== null) fields.description_en = description_en;

    const description_az = formData.get('description_az');
    if (description_az !== null) fields.description_az = description_az;

    await updateItem('partners', params.id!, fields, image && image.size > 0 ? image : undefined);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update partner error:', error);
    return errorResponse('Failed to update partner', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    await deleteItem('partners', params.id!);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete partner error:', error);
    return errorResponse('Failed to delete partner', 500);
  }
};
