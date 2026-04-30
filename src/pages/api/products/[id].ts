import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';
import { validateImageFile, uploadImage, deleteImage, errorResponse, successResponse } from '../../../lib/api-helpers';
import { requireAuth } from '../../../lib/auth';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  try {
    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Get product
    const { results: productResults } = await db.prepare(
      'SELECT * FROM products WHERE id = ?'
    ).bind(params.id).all();

    const product = productResults?.[0];

    if (!product) {
      return errorResponse('Product not found', 404);
    }

    // Get images
    const { results: images } = await db.prepare(
      'SELECT * FROM product_images WHERE product_id = ? ORDER BY order_index ASC'
    ).bind(params.id).all();

    // Get specifications
    const { results: specifications } = await db.prepare(
      'SELECT * FROM product_specifications WHERE product_id = ? ORDER BY order_index ASC'
    ).bind(params.id).all();

    return successResponse({
      ...product,
      images: images || [],
      specifications: specifications || []
    });
  } catch (error) {
    console.error('Get product error:', error);
    return errorResponse('Failed to fetch product', 500);
  }
};

export const PUT: APIRoute = async ({ params, request, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const formData = await request.formData();

    const name = formData.get('name');
    const name_en = formData.get('name_en');
    const name_az = formData.get('name_az');
    const description = formData.get('description');
    const description_en = formData.get('description_en');
    const description_az = formData.get('description_az');
    const in_stock = formData.get('in_stock');
    const specifications = formData.get('specifications'); // JSON string
    const deletedImages = formData.get('deleted_images'); // JSON array of image IDs to delete

    if (!name) {
      return errorResponse('Name is required');
    }

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Update product
    await db.prepare(
      `UPDATE products
       SET name = ?, name_en = ?, name_az = ?, description = ?, description_en = ?, description_az = ?, in_stock = ?, updated_at = CURRENT_TIMESTAMP
       WHERE id = ?`
    ).bind(
      name,
      name_en || null,
      name_az || null,
      description || null,
      description_en || null,
      description_az || null,
      in_stock === 'true' || in_stock === '1' ? 1 : 0,
      params.id
    ).run();

    // Handle deleted images
    if (deletedImages) {
      const deletedIds = JSON.parse(deletedImages);
      for (const imageId of deletedIds) {
        // Get image URL before deleting
        const { results: imgResults } = await db.prepare(
          'SELECT image_url FROM product_images WHERE id = ?'
        ).bind(imageId).all();

        if (imgResults && imgResults[0]?.image_url) {
          try {
            await deleteImage(imgResults[0].image_url as string);
          } catch (error) {
            console.error('Error deleting image from KV:', error);
          }
        }

        await db.prepare('DELETE FROM product_images WHERE id = ?').bind(imageId).run();
      }
    }

    // Handle new images
    const images: File[] = [];
    for (const [key, value] of formData.entries()) {
      if (key.startsWith('image_') && value instanceof File && value.size > 0) {
        images.push(value);
      }
    }

    // Get current max order_index for images
    const { results: maxImageResults } = await db.prepare(
      'SELECT MAX(order_index) as max_order FROM product_images WHERE product_id = ?'
    ).bind(params.id).all();
    let maxImageOrder = maxImageResults?.[0]?.max_order || 0;

    // Upload new images
    for (const image of images) {
      const imageError = validateImageFile(image);
      if (imageError) {
        return errorResponse(imageError);
      }

      const imageUrl = await uploadImage(image, 'products');
      maxImageOrder++;

      await db.prepare(
        'INSERT INTO product_images (product_id, image_url, order_index) VALUES (?, ?, ?)'
      ).bind(params.id, imageUrl, maxImageOrder).run();
    }

    // Update specifications - delete all and recreate
    await db.prepare('DELETE FROM product_specifications WHERE product_id = ?').bind(params.id).run();

    if (specifications) {
      const specsArray = JSON.parse(specifications);
      for (let i = 0; i < specsArray.length; i++) {
        const spec = specsArray[i];
        if (spec.key && spec.value) {
          await db.prepare(
            `INSERT INTO product_specifications (product_id, key, key_en, key_az, value, value_en, value_az, order_index)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
          ).bind(
            params.id,
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

    return successResponse({ success: true });
  } catch (error) {
    console.error('Update product error:', error);
    return errorResponse('Failed to update product', 500);
  }
};

export const DELETE: APIRoute = async ({ params, cookies }) => {
  try {
    // Check authentication
    const authError = await requireAuth(cookies);
    if (authError) return authError;

    const db = env.DB;
    if (!db) {
      return errorResponse('Database not available', 500);
    }

    // Get all images for this product
    const { results: images } = await db.prepare(
      'SELECT image_url FROM product_images WHERE product_id = ?'
    ).bind(params.id).all();

    // Delete images from KV
    for (const img of images || []) {
      if (img.image_url) {
        try {
          await deleteImage(img.image_url as string);
        } catch (error) {
          console.error('Error deleting image:', error);
        }
      }
    }

    // Delete product (CASCADE will delete images and specifications from DB)
    await db.prepare('DELETE FROM products WHERE id = ?').bind(params.id).run();

    return successResponse({ success: true });
  } catch (error) {
    console.error('Delete product error:', error);
    return errorResponse('Failed to delete product', 500);
  }
};
