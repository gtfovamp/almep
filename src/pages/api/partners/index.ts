import type { APIRoute } from 'astro';

export const GET: APIRoute = async ({ locals }) => {
  try {
    const db = locals.runtime.env.DB;
    const { results } = await db.prepare(
      'SELECT * FROM partners ORDER BY order_index ASC'
    ).all();

    return new Response(JSON.stringify(results), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: 'Failed to fetch partners' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};

export const POST: APIRoute = async ({ request, locals }) => {
  try {
    const db = locals.runtime.env.DB;
    const data = await request.json();

    const { name, name_en, name_az, description, description_en, description_az, image_url, order_index } = data;

    const result = await db.prepare(
      `INSERT INTO partners (name, name_en, name_az, description, description_en, description_az, image_url, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
    ).bind(name, name_en, name_az, description, description_en, description_az, image_url, order_index || 0).run();

    return new Response(JSON.stringify({ id: result.meta.last_row_id }), {
      status: 201,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    return new Response(JSON.stringify({ error: 'Failed to create partner' }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
