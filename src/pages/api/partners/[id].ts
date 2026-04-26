import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const db = env.DB;
    const id = params.id;

    if (!db) {
      return new Response(JSON.stringify({ error: 'Database not available' }), {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    const result = await db.prepare('SELECT * FROM partners WHERE id = ?').bind(id).first();

    if (!result) {
      return new Response(JSON.stringify({ error: 'Partner not found' }), {
        status: 404,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    return new Response(JSON.stringify(result), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error fetching partner:', error);
    return new Response(JSON.stringify({ error: 'Failed to fetch partner' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};

export const PUT: APIRoute = async ({ params, request }) => {
  try {
    const db = env.DB;
    const kv = env.KV;
    const id = params.id;

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
    const image = formData.get('image') as File | null;

    let imageUrl: string | undefined;

    // If new image uploaded, upload to KV
    if (image && image.size > 0) {
      const imageKey = `partners/${Date.now()}-${image.name}`;
      const imageBuffer = await image.arrayBuffer();

      await kv.put(imageKey, imageBuffer, {
        metadata: {
          contentType: image.type,
          fileName: image.name,
          uploadedAt: new Date().toISOString()
        }
      });
      imageUrl = `/images/${imageKey}`;
    }

    // Update database
    const updateFields = [];
    const values = [];

    if (name) {
      updateFields.push('name = ?');
      values.push(name);
    }
    if (name_en !== undefined) {
      updateFields.push('name_en = ?');
      values.push(name_en);
    }
    if (name_az !== undefined) {
      updateFields.push('name_az = ?');
      values.push(name_az);
    }
    if (description !== undefined) {
      updateFields.push('description = ?');
      values.push(description);
    }
    if (description_en !== undefined) {
      updateFields.push('description_en = ?');
      values.push(description_en);
    }
    if (description_az !== undefined) {
      updateFields.push('description_az = ?');
      values.push(description_az);
    }
    if (imageUrl) {
      updateFields.push('image_url = ?');
      values.push(imageUrl);
    }

    updateFields.push('updated_at = CURRENT_TIMESTAMP');
    values.push(id);

    await db.prepare(
      `UPDATE partners SET ${updateFields.join(', ')} WHERE id = ?`
    ).bind(...values).run();

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error updating partner:', error);
    return new Response(JSON.stringify({ error: 'Failed to update partner' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};

export const DELETE: APIRoute = async ({ params }) => {
  try {
    const db = env.DB;
    const kv = env.KV;
    const id = params.id;

    if (!db || !kv) {
      return new Response(JSON.stringify({ error: 'Database or storage not available' }), {
        status: 503,
        headers: { 'Content-Type': 'application/json' }
      });
    }

    // Get image URL before deleting
    const { results } = await db.prepare('SELECT image_url FROM partners WHERE id = ?').bind(id).all();
    const item = results[0] as any;

    if (item?.image_url) {
      const imageKey = item.image_url.replace('/images/', '');
      await kv.delete(imageKey);
    }

    await db.prepare('DELETE FROM partners WHERE id = ?').bind(id).run();

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error deleting partner:', error);
    return new Response(JSON.stringify({ error: 'Failed to delete partner' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
