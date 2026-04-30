import { env } from 'cloudflare:workers';
import type { AstroCookies } from 'astro';

// Validation helpers
export function validateRequired(fields: Record<string, any>, required: string[]): string | null {
  for (const field of required) {
    if (!fields[field] || (typeof fields[field] === 'string' && fields[field].trim() === '')) {
      return `Field '${field}' is required`;
    }
  }
  return null;
}

export function validateImageFile(file: File): string | null {
  const MAX_SIZE = 5 * 1024 * 1024; // 5MB
  const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

  if (file.size > MAX_SIZE) {
    return 'File size must be less than 5MB';
  }

  if (!ALLOWED_TYPES.includes(file.type)) {
    return 'File must be an image (JPEG, PNG, GIF, or WebP)';
  }

  return null;
}

export function sanitizeFilename(filename: string): string {
  return filename.replace(/[^a-zA-Z0-9.-]/g, '_');
}

// Image upload helper
export async function uploadImage(file: File, prefix: string): Promise<string> {
  const kv = env.KV;
  const sanitized = sanitizeFilename(file.name);
  const imageKey = `${prefix}/${Date.now()}-${crypto.randomUUID()}-${sanitized}`;
  const imageBuffer = await file.arrayBuffer();

  await kv.put(imageKey, imageBuffer, {
    metadata: {
      contentType: file.type,
      fileName: file.name,
      uploadedAt: new Date().toISOString()
    }
  });

  return `/images/${imageKey}`;
}

// Delete image helper
export async function deleteImage(imageUrl: string): Promise<void> {
  const kv = env.KV;
  const imageKey = imageUrl.replace('/images/', '');
  await kv.delete(imageKey);
}

// Generic CRUD helpers
export async function createItem(
  table: string,
  fields: Record<string, any>,
  imageFile?: File,
  imageFieldName: string = 'image_url'
): Promise<{ id: number; imageUrl?: string }> {
  const db = env.DB;

  let imageUrl: string | undefined;
  if (imageFile) {
    imageUrl = await uploadImage(imageFile, table);
    fields[imageFieldName] = imageUrl;
  }

  // Get max order_index
  const { results: maxOrder } = await db.prepare(
    `SELECT MAX(order_index) as max_order FROM ${table}`
  ).all();
  fields.order_index = (maxOrder[0]?.max_order || 0) + 1;

  const columns = Object.keys(fields).join(', ');
  const placeholders = Object.keys(fields).map(() => '?').join(', ');
  const values = Object.values(fields);

  const result = await db.prepare(
    `INSERT INTO ${table} (${columns}) VALUES (${placeholders})`
  ).bind(...values).run();

  return { id: result.meta.last_row_id as number, imageUrl };
}

export async function updateItem(
  table: string,
  id: string | number,
  fields: Record<string, any>,
  imageFile?: File,
  imageFieldName: string = 'image_url'
): Promise<{ imageUrl?: string }> {
  const db = env.DB;

  let imageUrl: string | undefined;
  if (imageFile) {
    // Delete old image
    const oldItem = await db.prepare(`SELECT ${imageFieldName} FROM ${table} WHERE id = ?`).bind(id).first();
    if (oldItem?.[imageFieldName]) {
      await deleteImage(oldItem[imageFieldName] as string);
    }

    imageUrl = await uploadImage(imageFile, table);
    fields[imageFieldName] = imageUrl;
  }

  fields.updated_at = new Date().toISOString();

  const updates = Object.keys(fields).map(key => `${key} = ?`).join(', ');
  const values = [...Object.values(fields), id];

  await db.prepare(
    `UPDATE ${table} SET ${updates} WHERE id = ?`
  ).bind(...values).run();

  return { imageUrl };
}

export async function deleteItem(table: string, id: string | number, imageFieldName: string = 'image_url'): Promise<void> {
  const db = env.DB;

  // Try to delete image if field exists
  try {
    const item = await db.prepare(`SELECT ${imageFieldName} FROM ${table} WHERE id = ?`).bind(id).first();
    if (item?.[imageFieldName]) {
      await deleteImage(item[imageFieldName] as string);
    }
  } catch (error) {
    // Field might not exist, continue with deletion
    console.log(`No ${imageFieldName} field in ${table}`);
  }

  await db.prepare(`DELETE FROM ${table} WHERE id = ?`).bind(id).run();
}

export async function reorderItems(table: string, items: Array<{ id: number; order: number }>): Promise<void> {
  const db = env.DB;

  for (const item of items) {
    await db.prepare(
      `UPDATE ${table} SET order_index = ? WHERE id = ?`
    ).bind(item.order, item.id).run();
  }
}

export async function getItems(table: string): Promise<any[]> {
  const db = env.DB;
  const { results } = await db.prepare(
    `SELECT * FROM ${table} ORDER BY order_index ASC`
  ).all();
  return results;
}

export async function getItem(table: string, id: string | number): Promise<any | null> {
  const db = env.DB;
  return await db.prepare(`SELECT * FROM ${table} WHERE id = ?`).bind(id).first();
}

// Rate limiting helper (simple in-memory, for production use KV or D1)
const rateLimitMap = new Map<string, { count: number; resetAt: number }>();

export function checkRateLimit(key: string, maxRequests: number, windowMs: number): boolean {
  const now = Date.now();
  const record = rateLimitMap.get(key);

  if (!record || now > record.resetAt) {
    rateLimitMap.set(key, { count: 1, resetAt: now + windowMs });
    return true;
  }

  if (record.count >= maxRequests) {
    return false;
  }

  record.count++;
  return true;
}

// Clean up old rate limit records periodically
setInterval(() => {
  const now = Date.now();
  for (const [key, record] of rateLimitMap.entries()) {
    if (now > record.resetAt) {
      rateLimitMap.delete(key);
    }
  }
}, 60000); // Clean up every minute

// Error response helper
export function errorResponse(message: string, status: number = 400) {
  return new Response(JSON.stringify({ error: message }), {
    status,
    headers: { 'Content-Type': 'application/json' }
  });
}

export function successResponse(data: any, status: number = 200) {
  return new Response(JSON.stringify(data), {
    status,
    headers: { 'Content-Type': 'application/json' }
  });
}

// Session cleanup helper
export async function cleanupExpiredSessions(): Promise<number> {
  const db = env.DB;
  const result = await db.prepare(
    `DELETE FROM admin_sessions WHERE expires_at < datetime('now')`
  ).run();
  return result.meta.changes || 0;
}

// Password validation
export function validatePassword(password: string): string | null {
  if (password.length < 8) {
    return 'Password must be at least 8 characters long';
  }
  if (!/[a-zA-Z]/.test(password)) {
    return 'Password must contain at least one letter';
  }
  if (!/[0-9]/.test(password)) {
    return 'Password must contain at least one number';
  }
  return null;
}

// Username validation
export function validateUsername(username: string): string | null {
  if (username.length < 3) {
    return 'Username must be at least 3 characters long';
  }
  if (username.length > 20) {
    return 'Username must be less than 20 characters';
  }
  if (!/^[a-zA-Z0-9_]+$/.test(username)) {
    return 'Username can only contain letters, numbers, and underscores';
  }
  return null;
}
