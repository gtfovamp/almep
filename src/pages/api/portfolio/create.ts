import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  try {
    const db = env.DB;
    const kv = env.KV;

    if (!db || !kv) {
      return new Response(JSON.stringify({ error: 'Database or storage not available' }), {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    const formData = await request.formData();
    const title = formData.get('title') as string;
    const title_en = formData.get('title_en') as string;
    const title_az = formData.get('title_az') as string;
    const year = formData.get('year') as string;
    const description = formData.get('description') as string;
    const description_en = formData.get('description_en') as string;
    const description_az = formData.get('description_az') as string;
    const image = formData.get('image') as File;

    if (!title || !year || !image) {
      return new Response(JSON.stringify({ error: 'Missing required fields' }), {
        status: 400,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Upload image to KV
    const imageKey = `portfolio/${Date.now()}-${image.name}`;
    const imageBuffer = await image.arrayBuffer();

    await kv.put(imageKey, imageBuffer, {
      metadata: {
        contentType: image.type,
        fileName: image.name,
        uploadedAt: new Date().toISOString()
      }
    });

    const imageUrl = `/images/${imageKey}`;

    // Get max order_index
    const { results: maxOrder } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM portfolio'
    ).all();
    const nextOrder = (maxOrder[0]?.max_order || 0) + 1;

    // Insert into database
    const result = await db.prepare(
      `INSERT INTO portfolio (title, title_en, title_az, year, image_url, description, description_en, description_az, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`
    ).bind(title, title_en, title_az, year, imageUrl, description, description_en, description_az, nextOrder).run();

    return new Response(JSON.stringify({
      success: true,
      id: result.meta.last_row_id
    }), {
      status: 201,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error creating portfolio:', error);
    return new Response(JSON.stringify({ error: 'Failed to create portfolio item' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
