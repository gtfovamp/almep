import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const kv = env.KV;
    const key = params.key;

    if (!key) {
      return new Response('Not found', { status: 404 });
    }

    if (!kv) {
      return new Response('Storage not available', { status: 503 });
    }

    const { value, metadata } = await kv.getWithMetadata(key, 'arrayBuffer');

    if (!value) {
      return new Response('Not found', { status: 404 });
    }

    const headers = new Headers();
    headers.set('content-type', (metadata as any)?.contentType || 'image/jpeg');
    headers.set('cache-control', 'public, max-age=31536000, immutable');

    return new Response(value, {
      headers
    });
  } catch (error) {
    return new Response('Error fetching image', { status: 500 });
  }
};
