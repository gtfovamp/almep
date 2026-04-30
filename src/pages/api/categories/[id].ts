import type { APIRoute } from 'astro';
import { getItem, updateItem, deleteItem, validateImageFile, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const item = await getItem('categories', params.id!);

    if (!item) {
      return errorResponse('Category not found', 404);
    }

    return successResponse(item);
  } catch (error) {
    console.error('Get category error:', error);
    return errorResponse('Failed to fetch category', 500);
  }
};

export const PUT: APIRoute = async ({ params, request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();
    const icon = formData.get('icon') as File | null;

    // Validate image if provided
    if (icon && icon.size > 0) {
      const imageError = validateImageFile(icon);
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

    await updateItem('categories', params.id!, fields, icon && icon.size > 0 ? icon : undefined, 'icon_url');

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update category error:', error);
    return errorResponse('Failed to update category', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    await deleteItem('categories', params.id!, 'icon_url');

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete category error:', error);
    return errorResponse('Failed to delete category', 500);
  }
};
