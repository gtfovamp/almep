import type { APIRoute } from 'astro';

export const GET: APIRoute = async ({ request, locals }) => {
  const url = new URL(request.url);
  const query = url.searchParams.get('q');

  if (!query || query.length < 2) {
    return new Response(JSON.stringify({ results: [] }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  }

  try {
    const db = locals.runtime.env.DB;
    const searchTerm = `%${query.toLowerCase()}%`;

    // Поиск по продуктам
    const products = await db.prepare(`
      SELECT
        p.id,
        p.name_ru as title,
        p.slug,
        pi.url as image
      FROM products p
      LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
      WHERE LOWER(p.name_ru) LIKE ?
         OR LOWER(p.name_en) LIKE ?
         OR LOWER(p.name_az) LIKE ?
         OR LOWER(p.article) LIKE ?
      LIMIT 6
    `).bind(searchTerm, searchTerm, searchTerm, searchTerm).all();

    const results = products.results.map((product: any) => ({
      title: product.title,
      url: `/ru/products/${product.slug}`,
      image: product.image || '/assets/placeholder.png'
    }));

    return new Response(JSON.stringify({ results }), {
      status: 200,
      headers: { 'Content-Type': 'application/json' }
    });
  } catch (error) {
    console.error('Search error:', error);
    return new Response(JSON.stringify({ results: [] }), {
      status: 500,
      headers: { 'Content-Type': 'application/json' }
    });
  }
};
