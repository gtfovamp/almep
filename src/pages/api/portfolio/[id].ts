import type { APIRoute } from 'astro';
import { getItem, updateItem, deleteItem, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const item = await getItem('portfolio', params.id!);

    if (!item) {
      return errorResponse('Portfolio item not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get portfolio error:', error);
    return errorResponse('Failed to fetch portfolio item', 500);
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

    const year = formData.get('year');
    if (year) fields.year = year;

    const description = formData.get('description');
    if (description !== null) fields.description = description;

    const description_en = formData.get('description_en');
    if (description_en !== null) fields.description_en = description_en;

    const description_az = formData.get('description_az');
    if (description_az !== null) fields.description_az = description_az;

    await updateItem('portfolio', params.id!, fields, image && image.size > 0 ? image : undefined);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update portfolio error:', error);
    return errorResponse('Failed to update portfolio item', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    await deleteItem('portfolio', params.id!);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete portfolio error:', error);
    return errorResponse('Failed to delete portfolio item', 500);
  }
};
