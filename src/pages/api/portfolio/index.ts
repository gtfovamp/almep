import type { APIRoute } from 'astro';
import { getItems, errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const items = await getItems('portfolio');
    return successResponse(items);
  } catch (error) {
    console.error('Get portfolio items error:', error);
    return errorResponse('Failed to fetch portfolio items', 500);
  }
};
