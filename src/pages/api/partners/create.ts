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
    const name = formData.get('name') as string;
    const name_en = formData.get('name_en') as string;
    const name_az = formData.get('name_az') as string;
    const description = formData.get('description') as string;
    const description_en = formData.get('description_en') as string;
    const description_az = formData.get('description_az') as string;
    const image = formData.get('image') as File;

    if (!name || !image) {
      return new Response(JSON.stringify({ error: 'Missing required fields' }), {
        status: 400,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Upload image to KV
    const imageKey = `partners/${Date.now()}-${image.name}`;
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
      'SELECT MAX(order_index) as max_order FROM partners'
    ).all();
    const nextOrder = (maxOrder[0]?.max_order || 0) + 1;

    // Insert into database
    const result = await db.prepare(
      `INSERT INTO partners (name, name_en, name_az, description, description_en, description_az, image_url, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
    ).bind(name, name_en, name_az, description, description_en, description_az, imageUrl, nextOrder).run();

    return new Response(JSON.stringify({
      success: true,
      id: result.meta.last_row_id
    }), {
      status: 201,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error creating partner:', error);
    return new Response(JSON.stringify({ error: 'Failed to create partner' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
