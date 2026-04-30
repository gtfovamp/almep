import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { validateImageFile, uploadImage, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const POST: APIRoute = async ({ request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();

    const subcategory_id = formData.get('subcategory_id');
    const name = formData.get('name');
    const name_en = formData.get('name_en');
    const name_az = formData.get('name_az');
    const description = formData.get('description');
    const description_en = formData.get('description_en');
    const description_az = formData.get('description_az');
    const in_stock = formData.get('in_stock');
    const specifications = formData.get('specifications'); // JSON string

    if (!subcategory_id || !name) {
      return errorResponse('Subcategory ID and name are required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Get max order_index for this subcategory
    const { results: maxResults } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM products WHERE subcategory_id = ?'
    ).bind(subcategory_id).all();
    const maxOrder = maxResults?.[0]?.max_order || 0;

    // Insert product
    const result = await db.prepare(
      `INSERT INTO products (subcategory_id, name, name_en, name_az, description, description_en, description_az, in_stock, order_index)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`
    ).bind(
      subcategory_id,
      name,
      name_en || null,
      name_az || null,
      description || null,
      description_en || null,
      description_az || null,
      in_stock === 'true' || in_stock === '1' ? 1 : 0,
      maxOrder + 1
    ).run();

    const productId = result.meta.last_row_id;

    // Handle multiple images
    const images: File[] = [];
    for (const [key, value] of formData.entries()) {
      if (key.startsWith('image_') && value instanceof File && value.size > 0) {
        images.push(value);
      }
    }

    // Upload images and save to product_images table
    for (let i = 0; i < images.length; i++) {
      const image = images[i];
      const imageError = validateImageFile(image);
      if (imageError) {
        return errorResponse(imageError);
      }

      const imageUrl = await uploadImage(image, 'products');

      await db.prepare(
        'INSERT INTO product_images (product_id, image_url, order_index) VALUES (?, ?, ?)'
      ).bind(productId, imageUrl, i).run();
    }

    // Handle specifications
    if (specifications) {
      const specsArray = JSON.parse(specifications);
      for (let i = 0; i < specsArray.length; i++) {
        const spec = specsArray[i];
        if (spec.key && spec.value) {
          await db.prepare(
            `INSERT INTO product_specifications (product_id, key, key_en, key_az, value, value_en, value_az, order_index)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
          ).bind(
            productId,
            spec.key,
            spec.key_en || null,
            spec.key_az || null,
            spec.value,
            spec.value_en || null,
            spec.value_az || null,
            i
          ).run();
        }
      }
    }

    return successResponse({ success: true, id: productId });
  } catch (error) {
    console.error('Create product error:', error);
    return errorResponse('Failed to create product', 500);
  }
};
