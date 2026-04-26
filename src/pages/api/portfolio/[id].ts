import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

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
    const title = formData.get('title') as string;
    const title_en = formData.get('title_en') as string;
    const title_az = formData.get('title_az') as string;
    const year = formData.get('year') as string;
    const description = formData.get('description') as string;
    const description_en = formData.get('description_en') as string;
    const description_az = formData.get('description_az') as string;
    const image = formData.get('image') as File | null;

    let imageUrl: string | undefined;

    // If new image uploaded, upload to KV
    if (image && image.size > 0) {
      const imageKey = `portfolio/${Date.now()}-${image.name}`;
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

    if (title) {
      updateFields.push('title = ?');
      values.push(title);
    }
    if (title_en) {
      updateFields.push('title_en = ?');
      values.push(title_en);
    }
    if (title_az) {
      updateFields.push('title_az = ?');
      values.push(title_az);
    }
    if (year) {
      updateFields.push('year = ?');
      values.push(year);
    }
    if (description) {
      updateFields.push('description = ?');
      values.push(description);
    }
    if (description_en) {
      updateFields.push('description_en = ?');
      values.push(description_en);
    }
    if (description_az) {
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
      `UPDATE portfolio SET ${updateFields.join(', ')} WHERE id = ?`
    ).bind(...values).run();

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error updating portfolio:', error);
    return new Response(JSON.stringify({ error: 'Failed to update portfolio item' }), {
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
    const { results } = await db.prepare('SELECT image_url FROM portfolio WHERE id = ?').bind(id).all();
    const item = results[0] as any;

    if (item?.image_url) {
      const imageKey = item.image_url.replace('/images/', '');
      await kv.delete(imageKey);
    }

    await db.prepare('DELETE FROM portfolio WHERE id = ?').bind(id).run();

    return new Response(JSON.stringify({ success: true }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Error deleting portfolio:', error);
    return new Response(JSON.stringify({ error: 'Failed to delete portfolio item' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
