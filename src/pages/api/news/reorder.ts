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

    await reorderItems('news', items);

    return successResponse({ success: true });
  } catch (error) {
    console.error('Reorder news error:', error);
    return errorResponse('Failed to reorder news items', 500);
  }
};
