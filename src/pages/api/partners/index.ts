import type { APIRoute } from 'astro';
import { getItems, errorResponse, successResponse } from '../../../lib/api-helpers';

export const prerender = false;

export const GET: APIRoute = async () => {
  try {
    const items = await getItems('partners');
    return successResponse(items);
  } catch (error) {
    console.error('Get partners error:', error);
    return errorResponse('Failed to fetch partners', 500);
  }
};
