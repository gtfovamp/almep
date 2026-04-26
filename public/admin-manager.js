// Admin Panel Common JavaScript Functions

class AdminManager {
  constructor(config) {
    this.apiEndpoint = config.apiEndpoint;
    this.formId = config.formId;
    this.listId = config.listId;
    this.editingId = null;

    this.form = document.getElementById(this.formId);
    this.formTitle = document.getElementById('formTitle');
    this.submitBtn = document.getElementById('submitBtn');
    this.cancelBtn = document.getElementById('cancelEdit');
    this.imageInput = document.getElementById('image');
    this.fileUpload = document.getElementById('fileUpload');
    this.list = document.getElementById(this.listId);

    this.init();
  }

  init() {
    this.setupFileUpload();
    this.setupForm();
    this.setupDragAndDrop();

    if (this.cancelBtn) {
      this.cancelBtn.addEventListener('click', () => this.resetForm());
    }
  }

  setupFileUpload() {
    if (!this.imageInput || !this.fileUpload) return;

    const placeholder = this.fileUpload.querySelector('.file-upload-placeholder');
    const preview = this.fileUpload.querySelector('.file-upload-preview');
    const previewImg = preview?.querySelector('img');
    const removeBtn = this.fileUpload.querySelector('.file-upload-remove');

    this.imageInput.addEventListener('change', (e) => {
      const file = e.target.files?.[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          previewImg.src = e.target.result;
          placeholder.style.display = 'none';
          preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });

    removeBtn?.addEventListener('click', () => {
      this.imageInput.value = '';
      placeholder.style.display = 'flex';
      preview.style.display = 'none';
    });
  }

  setupForm() {
    if (!this.form) return;

    this.form.addEventListener('submit', async (e) => {
      e.preventDefault();
      await this.handleSubmit();
    });
  }

  async handleSubmit() {
    const formData = new FormData(this.form);
    const url = this.editingId
      ? `${this.apiEndpoint}/${this.editingId}`
      : `${this.apiEndpoint}/create`;
    const method = this.editingId ? 'PUT' : 'POST';

    this.submitBtn.disabled = true;
    this.submitBtn.textContent = 'Saving...';

    try {
      const response = await fetch(url, { method, body: formData });

      if (response.ok) {
        window.location.reload();
      } else {
        alert('Error saving item');
        this.submitBtn.disabled = false;
        this.submitBtn.textContent = this.editingId ? 'Save Changes' : 'Add Item';
      }
    } catch (error) {
      alert('Network error');
      this.submitBtn.disabled = false;
      this.submitBtn.textContent = this.editingId ? 'Save Changes' : 'Add Item';
    }
  }

  async edit(id) {
    try {
      const response = await fetch(`${this.apiEndpoint}/${id}`);
      const item = await response.json();

      if (!item) return;

      this.editingId = id;
      this.populateForm(item);
      this.setEditMode(true);

      document.getElementById('formSection')?.scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
      alert('Error loading item');
    }
  }

  async delete(id) {
    if (!confirm('Are you sure you want to delete this item?')) return;

    try {
      const response = await fetch(`${this.apiEndpoint}/${id}`, { method: 'DELETE' });
      if (response.ok) {
        window.location.reload();
      } else {
        alert('Error deleting item');
      }
    } catch (error) {
      alert('Network error');
    }
  }

  populateForm(item) {
    // Override in subclass
  }

  setEditMode(isEdit) {
    if (isEdit) {
      this.formTitle.textContent = 'Edit Item';
      this.submitBtn.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M16 4L7 13L3 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Save Changes
      `;
      this.cancelBtn.style.display = 'flex';
      this.imageInput.required = false;
    } else {
      this.formTitle.textContent = 'Add New Item';
      this.submitBtn.innerHTML = `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
          <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        Add Item
      `;
      this.cancelBtn.style.display = 'none';
      this.imageInput.required = true;
    }
  }

  resetForm() {
    this.editingId = null;
    this.form.reset();

    const placeholder = this.fileUpload?.querySelector('.file-upload-placeholder');
    const preview = this.fileUpload?.querySelector('.file-upload-preview');

    if (placeholder) placeholder.style.display = 'flex';
    if (preview) preview.style.display = 'none';

    this.setEditMode(false);
  }

  setupDragAndDrop() {
    if (!this.list) return;

    let draggedElement = null;

    this.list.addEventListener('dragstart', (e) => {
      const target = e.target;
      if (target.classList.contains('item-card')) {
        draggedElement = target;
        target.classList.add('dragging');
      }
    });

    this.list.addEventListener('dragend', (e) => {
      const target = e.target;
      if (target.classList.contains('item-card')) {
        target.classList.remove('dragging');
        draggedElement = null;
      }
    });

    this.list.addEventListener('dragover', (e) => {
      e.preventDefault();
      const target = e.target.closest('.item-card');

      if (target && draggedElement && target !== draggedElement) {
        const items = Array.from(this.list.querySelectorAll('.item-card'));
        const draggedIndex = items.indexOf(draggedElement);
        const targetIndex = items.indexOf(target);

        if (draggedIndex < targetIndex) {
          target.after(draggedElement);
        } else {
          target.before(draggedElement);
        }
      }
    });

    this.list.addEventListener('drop', async (e) => {
      e.preventDefault();
      await this.handleReorder();
    });
  }

  async handleReorder() {
    const items = Array.from(this.list.querySelectorAll('.item-card'));
    const newOrder = items.map((item, index) => ({
      id: parseInt(item.dataset.id || '0'),
      order_index: index + 1
    }));

    try {
      await fetch(`${this.apiEndpoint}/reorder`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ items: newOrder })
      });
    } catch (error) {
      console.error('Reorder error:', error);
      window.location.reload();
    }
  }
}

// Portfolio Manager
class PortfolioManager extends AdminManager {
  populateForm(item) {
    document.getElementById('title').value = item.title || '';
    document.getElementById('title_en').value = item.title_en || '';
    document.getElementById('title_az').value = item.title_az || '';
    document.getElementById('year').value = item.year || '';
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

  setEditMode(isEdit) {
    super.setEditMode(isEdit);
    if (isEdit) {
      this.formTitle.textContent = 'Edit Project';
    } else {
      this.formTitle.textContent = 'Add New Project';
    }
  }
}

// Partners Manager
class PartnersManager extends AdminManager {
  populateForm(item) {
    document.getElementById('name').value = item.name || '';
    document.getElementById('name_en').value = item.name_en || '';
    document.getElementById('name_az').value = item.name_az || '';
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

  setEditMode(isEdit) {
    super.setEditMode(isEdit);
    if (isEdit) {
      this.formTitle.textContent = 'Edit Partner';
    } else {
      this.formTitle.textContent = 'Add New Partner';
    }
  }
}
