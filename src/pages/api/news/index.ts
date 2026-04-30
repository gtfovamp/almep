import type { APIRoute } from 'astro';
import { getItems, errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const items = await getItems('news');
    return successResponse(items);
  } catch (error) {
    console.error('Get news items error:', error);
    return errorResponse('Failed to fetch news items', 500);
  }
};
