import type { APIRoute } from 'astro';
import { reorderItems, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const { items } = await request.json();

    if (!Array.isArray(items)) {
      return errorResponse('Invalid data format');
    }

    await reorderItems('categories', items);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Reorder categories error:', error);
    return errorResponse('Failed to reorder categories', 500);
  }
};
