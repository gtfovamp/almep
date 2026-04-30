// Product Manager - handles products with multiple images and specifications

class ProductManager {
  constructor() {
    this.form = document.getElementById('productForm');
    this.editingId = null;
    this.images = []; // Array of File objects
    this.existingImages = []; // Array of existing image objects {id, url}
    this.deletedImageIds = [];
    this.specifications = [];

    this.setupForm();
    this.setupCategoryChange();
    this.setupImageUpload();
    this.setupSpecifications();
    this.setupFilters();
    this.setupDragAndDrop();

    // Initialize with 3 default specifications
    for (let i = 0; i < 3; i++) {
      this.addSpecification();
    }
  }

  setupForm() {
    this.form.addEventListener('submit', (e) => this.handleSubmit(e));

    const cancelBtn = document.getElementById('cancelEdit');
    cancelBtn?.addEventListener('click', () => this.resetForm());
  }

  setupCategoryChange() {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');

    categorySelect.addEventListener('change', () => {
      const categoryId = categorySelect.value;
      subcategorySelect.innerHTML = '<option value="">Select a subcategory</option>';

      if (categoryId) {
        const filtered = window.allSubcategories.filter(s => s.category_id == categoryId);
        filtered.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.name;
          subcategorySelect.appendChild(option);
        });
        subcategorySelect.disabled = false;
      } else {
        subcategorySelect.disabled = true;
      }
    });
  }

  setupImageUpload() {
    const addImageBtn = document.getElementById('addImageBtn');
    const imageInput = document.getElementById('imageInput');
    const imagesGrid = document.getElementById('imagesGrid');

    addImageBtn.addEventListener('click', () => {
      imageInput.click();
    });

    imageInput.addEventListener('change', (e) => {
      const files = Array.from(e.target.files);
      files.forEach(file => {
        this.images.push(file);
        this.renderImage(file);
      });
      imageInput.value = '';
    });
  }

  renderImage(file, existingImage = null) {
    const imagesGrid = document.getElementById('imagesGrid');
    const addBtn = document.getElementById('addImageBtn');

    const imageItem = document.createElement('div');
    imageItem.className = 'image-item';
    imageItem.draggable = true;

    const img = document.createElement('img');
    if (existingImage) {
      img.src = existingImage.url;
      imageItem.dataset.imageId = existingImage.id;
    } else {
      const reader = new FileReader();
      reader.onload = (e) => {
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }

    const removeBtn = document.createElement('button');
    removeBtn.className = 'image-item-remove';
    removeBtn.type = 'button';
    removeBtn.innerHTML = '×';
    removeBtn.addEventListener('click', () => {
      if (existingImage) {
        this.deletedImageIds.push(existingImage.id);
        this.existingImages = this.existingImages.filter(img => img.id !== existingImage.id);
      } else {
        const index = this.images.indexOf(file);
        if (index > -1) {
          this.images.splice(index, 1);
        }
      }
      imageItem.remove();
    });

    imageItem.appendChild(img);
    imageItem.appendChild(removeBtn);
    imagesGrid.insertBefore(imageItem, addBtn);
  }

  setupSpecifications() {
    const addSpecBtn = document.getElementById('addSpecBtn');
    addSpecBtn.addEventListener('click', () => {
      this.addSpecification();
    });
  }

  addSpecification(spec = null) {
    const container = document.getElementById('specsContainer');
    const index = this.specifications.length;

    const specItem = document.createElement('div');
    specItem.className = 'spec-item';
    specItem.dataset.index = index;

    specItem.innerHTML = `
      <div class="spec-group">
        <label>Key (RU)</label>
        <input type="text" class="spec-key" placeholder="e.g., Мощность" value="${spec?.key || ''}" />
        <div class="spec-langs">
          <input type="text" class="spec-key-en" placeholder="EN" value="${spec?.key_en || ''}" />
          <input type="text" class="spec-key-az" placeholder="AZ" value="${spec?.key_az || ''}" />
        </div>
      </div>
      <div class="spec-group">
        <label>Value (RU)</label>
        <input type="text" class="spec-value" placeholder="e.g., 100 кВт" value="${spec?.value || ''}" />
        <div class="spec-langs">
          <input type="text" class="spec-value-en" placeholder="EN" value="${spec?.value_en || ''}" />
          <input type="text" class="spec-value-az" placeholder="AZ" value="${spec?.value_az || ''}" />
        </div>
      </div>
      <button type="button" class="btn-remove-spec">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 4L4 12M4 4L12 12" stroke-linecap="round"/>
        </svg>
      </button>
    `;

    const removeBtn = specItem.querySelector('.btn-remove-spec');
    removeBtn.addEventListener('click', () => {
      specItem.remove();
      this.specifications.splice(index, 1);
    });

    container.appendChild(specItem);
    this.specifications.push({});
  }

  collectSpecifications() {
    const specs = [];
    const specItems = document.querySelectorAll('.spec-item');

    specItems.forEach(item => {
      const key = item.querySelector('.spec-key').value;
      const key_en = item.querySelector('.spec-key-en').value;
      const key_az = item.querySelector('.spec-key-az').value;
      const value = item.querySelector('.spec-value').value;
      const value_en = item.querySelector('.spec-value-en').value;
      const value_az = item.querySelector('.spec-value-az').value;

      if (key && value) {
        specs.push({ key, key_en, key_az, value, value_en, value_az });
      }
    });

    return specs;
  }

  setupFilters() {
    const filterCategory = document.getElementById('filterCategory');
    const filterSubcategory = document.getElementById('filterSubcategory');

    filterCategory.addEventListener('change', () => {
      const categoryId = filterCategory.value;
      filterSubcategory.innerHTML = '<option value="">All Subcategories</option>';

      if (categoryId) {
        const filtered = window.allSubcategories.filter(s => s.category_id == categoryId);
        filtered.forEach(sub => {
          const option = document.createElement('option');
          option.value = sub.id;
          option.textContent = sub.name;
          filterSubcategory.appendChild(option);
        });
        filterSubcategory.disabled = false;
      } else {
        filterSubcategory.disabled = true;
      }

      this.filterProducts();
    });

    filterSubcategory.addEventListener('change', () => {
      this.filterProducts();
    });
  }

  filterProducts() {
    const categoryId = document.getElementById('filterCategory').value;
    const subcategoryId = document.getElementById('filterSubcategory').value;
    const cards = document.querySelectorAll('#productList .item-card');
    let visibleCount = 0;

    cards.forEach(card => {
      let show = true;

      if (categoryId && card.dataset.category !== categoryId) {
        show = false;
      }

      if (subcategoryId && card.dataset.subcategory !== subcategoryId) {
        show = false;
      }

      card.style.display = show ? 'flex' : 'none';
      if (show) visibleCount++;
    });

    const itemCount = document.getElementById('itemCount');
    if (itemCount) {
      itemCount.textContent = `${visibleCount} ${visibleCount === 1 ? 'product' : 'products'}`;
    }
  }

  setupDragAndDrop() {
    const list = document.getElementById('productList');
    if (!list) return;

    let draggedItem = null;

    list.addEventListener('dragstart', (e) => {
      if (e.target.classList.contains('item-card')) {
        draggedItem = e.target;
        e.target.style.opacity = '0.5';
      }
    });

    list.addEventListener('dragend', (e) => {
      if (e.target.classList.contains('item-card')) {
        e.target.style.opacity = '1';
      }
    });

    list.addEventListener('dragover', (e) => {
      e.preventDefault();
      const afterElement = this.getDragAfterElement(list, e.clientY);
      if (afterElement == null) {
        list.appendChild(draggedItem);
      } else {
        list.insertBefore(draggedItem, afterElement);
      }
    });

    list.addEventListener('drop', async (e) => {
      e.preventDefault();
      await this.saveOrder();
    });
  }

  getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.item-card:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
      const box = child.getBoundingClientRect();
      const offset = y - box.top - box.height / 2;

      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: child };
      } else {
        return closest;
      }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
  }

  async saveOrder() {
    const items = [...document.querySelectorAll('#productList .item-card')].map((card, index) => ({
      id: card.dataset.id,
      order: index
    }));

    try {
      await fetch('/api/products/reorder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items })
      });
    } catch (error) {
      console.error('Reorder error:', error);
    }
  }

  async edit(id) {
    try {
      const response = await fetch(`/api/products/${id}`);
      const item = await response.json();

      this.editingId = id;
      this.existingImages = item.images || [];
      this.deletedImageIds = [];
      this.images = [];

      document.getElementById('formTitle').textContent = 'Edit Product';
      document.getElementById('submitBtn').innerHTML = `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M16 5L7 14L3 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Update Product
      `;
      document.getElementById('cancelEdit').style.display = 'flex';

      // Find category for this subcategory
      const subcategory = window.allSubcategories.find(s => s.id === item.subcategory_id);
      if (subcategory) {
        document.getElementById('category_id').value = subcategory.category_id;
        document.getElementById('category_id').dispatchEvent(new Event('change'));

        setTimeout(() => {
          document.getElementById('subcategory_id').value = item.subcategory_id;
        }, 100);
      }

      document.getElementById('name').value = item.name || '';
      document.getElementById('name_en').value = item.name_en || '';
      document.getElementById('name_az').value = item.name_az || '';
      document.getElementById('description').value = item.description || '';
      document.getElementById('description_en').value = item.description_en || '';
      document.getElementById('description_az').value = item.description_az || '';
      document.getElementById('in_stock').checked = item.in_stock === 1;

      // Render existing images
      const imagesGrid = document.getElementById('imagesGrid');
      const existingImageItems = imagesGrid.querySelectorAll('.image-item');
      existingImageItems.forEach(item => item.remove());

      this.existingImages.forEach(img => {
        this.renderImage(null, { id: img.id, url: img.image_url });
      });

      // Load specifications
      const specsContainer = document.getElementById('specsContainer');
      specsContainer.innerHTML = '';
      this.specifications = [];

      if (item.specifications && item.specifications.length > 0) {
        item.specifications.forEach(spec => {
          this.addSpecification(spec);
        });
      } else {
        // Add 3 default empty specs
        for (let i = 0; i < 3; i++) {
          this.addSpecification();
        }
      }

      document.getElementById('formSection').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
      console.error('Edit error:', error);
      alert('Failed to load product');
    }
  }

  async delete(id) {
    if (!confirm('Delete this product?')) return;

    try {
      const response = await fetch(`/api/products/${id}`, {
        method: 'DELETE'
      });

      if (response.ok) {
        window.location.reload();
      } else {
        alert('Failed to delete product');
      }
    } catch (error) {
      console.error('Delete error:', error);
      alert('Failed to delete product');
    }
  }

  async handleSubmit(e) {
    e.preventDefault();

    const formData = new FormData();

    // Basic fields
    formData.append('subcategory_id', document.getElementById('subcategory_id').value);
    formData.append('name', document.getElementById('name').value);
    formData.append('name_en', document.getElementById('name_en').value);
    formData.append('name_az', document.getElementById('name_az').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('description_en', document.getElementById('description_en').value);
    formData.append('description_az', document.getElementById('description_az').value);
    formData.append('in_stock', document.getElementById('in_stock').checked ? '1' : '0');

    // Add new images
    this.images.forEach((file, index) => {
      formData.append(`image_${index}`, file);
    });

    // Add deleted image IDs
    if (this.editingId && this.deletedImageIds.length > 0) {
      formData.append('deleted_images', JSON.stringify(this.deletedImageIds));
    }

    // Add specifications
    const specs = this.collectSpecifications();
    formData.append('specifications', JSON.stringify(specs));

    try {
      const url = this.editingId
        ? `/api/products/${this.editingId}`
        : '/api/products/create';

      const method = this.editingId ? 'PUT' : 'POST';

      const response = await fetch(url, { method, body: formData });

      if (response.ok) {
        window.location.reload();
      } else {
        const error = await response.json();
        alert(error.error || 'Failed to save product');
      }
    } catch (error) {
      console.error('Submit error:', error);
      alert('Failed to save product');
    }
  }

  resetForm() {
    this.form.reset();
    this.editingId = null;
    this.images = [];
    this.existingImages = [];
    this.deletedImageIds = [];

    document.getElementById('formTitle').textContent = 'Add New Product';
    document.getElementById('submitBtn').innerHTML = `
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
        <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      </svg>
      Add Product
    `;
    document.getElementById('cancelEdit').style.display = 'none';
    document.getElementById('subcategory_id').disabled = true;

    // Clear images
    const imagesGrid = document.getElementById('imagesGrid');
    const imageItems = imagesGrid.querySelectorAll('.image-item');
    imageItems.forEach(item => item.remove());

    // Reset specifications
    const specsContainer = document.getElementById('specsContainer');
    specsContainer.innerHTML = '';
    this.specifications = [];
    for (let i = 0; i < 3; i++) {
      this.addSpecification();
    }
  }
}

// Initialize
window.productManager = new ProductManager();
