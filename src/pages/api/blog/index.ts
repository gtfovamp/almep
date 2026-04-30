import type { APIRoute } from 'astro';
import { getItems, errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const items = await getItems('blog');
    return successResponse(items);
  } catch (error) {
    console.error('Get blog items error:', error);
    return errorResponse('Failed to fetch blog items', 500);
  }
};
