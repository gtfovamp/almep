# Adding New Components to Admin Panel

This guide shows how to add new content types to the admin panel using the existing infrastructure.

## Quick Overview

The admin panel uses a shared architecture:
- **admin-styles.css** - Common styles for all admin pages
- **admin-manager.js** - Base class with CRUD and drag&drop functionality
- **AdminLayout.astro** - Unified layout with sidebar navigation

## Step 1: Create Database Migration

Create a new migration file: `migrations/000X_create_[component].sql`

```sql
-- Migration: Create [component] table
-- Created: 2026-04-26

CREATE TABLE IF NOT EXISTS [component] (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  title TEXT NOT NULL,
  title_en TEXT,
  title_az TEXT,
  description TEXT,
  description_en TEXT,
  description_az TEXT,
  image_url TEXT NOT NULL,
  order_index INTEGER NOT NULL DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_[component]_order ON [component](order_index);
```

Apply migration:
```bash
npx wrangler d1 execute almep-db --local --file=migrations/000X_create_[component].sql
npx wrangler d1 execute almep-db --remote --file=migrations/000X_create_[component].sql
```

## Step 2: Create API Endpoints

Create three files in `src/pages/api/[component]/`:

### `index.ts` - List all items
```typescript
import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async () => {
  const db = env.DB;
  const { results } = await db.prepare(
    'SELECT * FROM [component] ORDER BY order_index ASC'
  ).all();

  return new Response(JSON.stringify(results), {
    status: 200,
    headers: { 'Content-Type': 'application/json' }
  });
};
```

### `create.ts` - Create new item
```typescript
import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  const db = env.DB;
  const kv = env.KV;
  const formData = await request.formData();

  const title = formData.get('title') as string;
  const image = formData.get('image') as File;

  // Upload image to KV
  const imageKey = `[component]/${Date.now()}-${image.name}`;
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
    'SELECT MAX(order_index) as max_order FROM [component]'
  ).all();
  const nextOrder = (maxOrder[0]?.max_order || 0) + 1;

  // Insert into database
  const result = await db.prepare(
    `INSERT INTO [component] (title, title_en, title_az, description, description_en, description_az, image_url, order_index)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`
  ).bind(
    title,
    formData.get('title_en'),
    formData.get('title_az'),
    formData.get('description'),
    formData.get('description_en'),
    formData.get('description_az'),
    imageUrl,
    nextOrder
  ).run();

  return new Response(JSON.stringify({ success: true, id: result.meta.last_row_id }), {
    status: 201,
    headers: { 'Content-Type': 'application/json' }
  });
};
```

### `[id].ts` - Get, update, delete item
```typescript
import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const GET: APIRoute = async ({ params }) => {
  const db = env.DB;
  const result = await db.prepare('SELECT * FROM [component] WHERE id = ?').bind(params.id).first();

  if (!result) {
    return new Response(JSON.stringify({ error: 'Not found' }), { status: 404 });
  }

  return new Response(JSON.stringify(result), { status: 200 });
};

export const PUT: APIRoute = async ({ params, request }) => {
  const db = env.DB;
  const kv = env.KV;
  const formData = await request.formData();
  const image = formData.get('image') as File | null;

  let imageUrl: string | undefined;

  if (image && image.size > 0) {
    const imageKey = `[component]/${Date.now()}-${image.name}`;
    const imageBuffer = await image.arrayBuffer();
    await kv.put(imageKey, imageBuffer, {
      metadata: { contentType: image.type, fileName: image.name }
    });
    imageUrl = `/images/${imageKey}`;
  }

  const updateFields = [];
  const values = [];

  if (formData.get('title')) {
    updateFields.push('title = ?');
    values.push(formData.get('title'));
  }
  // Add other fields...

  if (imageUrl) {
    updateFields.push('image_url = ?');
    values.push(imageUrl);
  }

  updateFields.push('updated_at = CURRENT_TIMESTAMP');
  values.push(params.id);

  await db.prepare(
    `UPDATE [component] SET ${updateFields.join(', ')} WHERE id = ?`
  ).bind(...values).run();

  return new Response(JSON.stringify({ success: true }), { status: 200 });
};

export const DELETE: APIRoute = async ({ params }) => {
  const db = env.DB;
  const kv = env.KV;

  // Get image URL before deleting
  const { results } = await db.prepare('SELECT image_url FROM [component] WHERE id = ?').bind(params.id).all();
  const item = results[0] as any;

  if (item?.image_url) {
    const imageKey = item.image_url.replace('/images/', '');
    await kv.delete(imageKey);
  }

  await db.prepare('DELETE FROM [component] WHERE id = ?').bind(params.id).run();

  return new Response(JSON.stringify({ success: true }), { status: 200 });
};
```

### `reorder.ts` - Reorder items
```typescript
import type { APIRoute } from 'astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

export const POST: APIRoute = async ({ request }) => {
  const db = env.DB;
  const { items } = await request.json();

  for (const item of items) {
    await db.prepare(
      'UPDATE [component] SET order_index = ? WHERE id = ?'
    ).bind(item.order_index, item.id).run();
  }

  return new Response(JSON.stringify({ success: true }), { status: 200 });
};
```

## Step 3: Create Admin Page

Create `src/pages/admin/[component].astro`:

```astro
---
import AdminLayout from '../../layouts/AdminLayout.astro';
import { env } from 'cloudflare:workers';

export const prerender = false;

let items = [];
try {
  const db = env.DB;
  if (db) {
    const { results } = await db.prepare(
      'SELECT * FROM [component] ORDER BY order_index ASC'
    ).all();
    items = results || [];
  }
} catch (error) {
  console.error('Error fetching items:', error);
}
---

<AdminLayout title="[Component]" activeSection="[component]">
  <link rel="stylesheet" href="/admin-styles.css">

  <div class="admin-content">
    <section class="form-section" id="formSection">
      <div class="section-header">
        <h2 id="formTitle">Add New Item</h2>
        <button type="button" class="btn btn-ghost" id="cancelEdit" style="display: none;">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
          Cancel
        </button>
      </div>

      <form id="itemForm" class="admin-form" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group">
            <label for="title">Title (RU) *</label>
            <input type="text" id="title" name="title" required />
          </div>
          <div class="form-group">
            <label for="title_en">Title (EN)</label>
            <input type="text" id="title_en" name="title_en" />
          </div>
        </div>

        <div class="form-group">
          <label for="title_az">Title (AZ)</label>
          <input type="text" id="title_az" name="title_az" />
        </div>

        <div class="form-group">
          <label for="description">Description (RU)</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="description_en">Description (EN)</label>
            <textarea id="description_en" name="description_en" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="description_az">Description (AZ)</label>
            <textarea id="description_az" name="description_az" rows="3"></textarea>
          </div>
        </div>

        <div class="form-group">
          <label for="image">Image *</label>
          <div class="file-upload" id="fileUpload">
            <input type="file" id="image" name="image" accept="image/*" required />
            <div class="file-upload-placeholder">
              <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                <path d="M24 16V32M16 24H32" stroke="#7c3aed" stroke-width="2" stroke-linecap="round"/>
                <rect x="4" y="4" width="40" height="40" rx="4" stroke="#7c3aed" stroke-width="2"/>
              </svg>
              <span>Click or drag image here</span>
            </div>
            <div class="file-upload-preview" style="display: none;">
              <img src="" alt="Preview" />
              <button type="button" class="file-upload-remove">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <path d="M15 5L5 15M5 5L15 15" stroke="white" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Add Item
          </button>
        </div>
      </form>
    </section>

    <section class="list-section">
      <div class="section-header">
        <h2>Existing Items</h2>
        <span class="item-count">{items.length} items</span>
      </div>

      {items.length === 0 ? (
        <div class="empty-state">
          <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
            <rect x="8" y="8" width="48" height="48" rx="4" stroke="#ccc" stroke-width="2"/>
            <path d="M32 24V40M24 32H40" stroke="#ccc" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <p>No items yet</p>
          <span>Add your first item using the form above</span>
        </div>
      ) : (
        <div class="item-list" id="itemList">
          {items.map((item: any) => (
            <div class="item-card" data-id={item.id} data-order={item.order_index} draggable="true">
              <div class="item-drag">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                  <circle cx="7" cy="5" r="1.5" fill="#999"/>
                  <circle cx="13" cy="5" r="1.5" fill="#999"/>
                  <circle cx="7" cy="10" r="1.5" fill="#999"/>
                  <circle cx="13" cy="10" r="1.5" fill="#999"/>
                  <circle cx="7" cy="15" r="1.5" fill="#999"/>
                  <circle cx="13" cy="15" r="1.5" fill="#999"/>
                </svg>
              </div>
              <div class="item-image">
                <img src={item.image_url} alt={item.title} />
              </div>
              <div class="item-info">
                <h3>{item.title}</h3>
                <div class="item-meta">
                  {item.title_en && <span class="meta-lang">EN</span>}
                  {item.title_az && <span class="meta-lang">AZ</span>}
                </div>
              </div>
              <div class="item-actions">
                <button class="btn btn-icon btn-edit" onclick={`manager.edit(${item.id})`}>
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M12.5 2.5L15.5 5.5L6 15H3V12L12.5 2.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <button class="btn btn-icon btn-danger" onclick={`manager.delete(${item.id})`}>
                  <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M3 5H15M7 8V13M11 8V13M4 5L5 15H13L14 5M7 5V3H11V5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </section>
  </div>

  <script src="/admin-manager.js"></script>
  <script>
    // Create custom manager if needed, or use base AdminManager
    class ComponentManager extends AdminManager {
      populateForm(item) {
        document.getElementById('title').value = item.title || '';
        document.getElementById('title_en').value = item.title_en || '';
        document.getElementById('title_az').value = item.title_az || '';
        document.getElementById('description').value = item.description || '';
        document.getElementById('description_en').value = item.description_en || '';
        document.getElementById('description_az').value = item.description_az || '';

        if (item.image_url) {
          const preview = this.fileUpload.querySelector('.file-upload-preview');
          const previewImg = preview?.querySelector('img');
          const placeholder = this.fileUpload.querySelector('.file-upload-placeholder');

          if (previewImg) previewImg.src = item.image_url;
          if (placeholder) placeholder.style.display = 'none';
          if (preview) preview.style.display = 'block';
        }
      }
    }

    const manager = new ComponentManager({
      apiEndpoint: '/api/[component]',
      formId: 'itemForm',
      listId: 'itemList'
    });
  </script>
</AdminLayout>
```

## Step 4: Add to Navigation

Update `src/layouts/AdminLayout.astro` to add the new section to the sidebar:

```astro
<div class="nav-section">
  <div class="nav-section-title">Content</div>
  <!-- ... existing items ... -->
  <a href="/admin/[component]" class={`nav-item ${activeSection === '[component]' ? 'active' : ''}`}>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <!-- Your icon SVG -->
    </svg>
    [Component Name]
  </a>
</div>
```

## That's It!

You now have a fully functional admin section with:
- ✅ CRUD operations
- ✅ Image upload to Cloudflare KV
- ✅ Drag & drop reordering
- ✅ Multi-language support
- ✅ Consistent UI/UX

All using the shared infrastructure!
