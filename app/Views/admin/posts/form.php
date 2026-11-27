<?php
$errors = $errors ?? [];
$post = $post ?? [];
$isEdit = !empty($isEdit);
$actionUrl = $isEdit
    ? base_url('/admin/posts/' . (int) ($post['id'] ?? 0) . '/update')
    : base_url('/admin/posts/store');
$thumbPath = ltrim((string) ($post['thumbnail'] ?? ''), '/');
$hasExistingThumb = $thumbPath !== '';
$previewSrc = $hasExistingThumb ? base_url('/' . $thumbPath) : asset('images/posts/post-1.svg');
?>
<div class="ta-page-header">
  <div>
    <h1 class="ta-page-title"><?= $isEdit ? 'Chỉnh sửa bài viết' : 'Thêm bài viết mới' ?></h1>
    <p class="ta-page-subtitle">Soạn thảo nội dung WYSIWYG, upload ảnh đại diện lên server và cấu hình thẻ SEO.</p>
  </div>
  <div class="ta-header-buttons">
    <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-btn ta-btn--outline">&larr; Quay lại danh sách</a>
  </div>
</div>

<div id="postFormErrorSummary" class="ta-error-summary<?= !empty($errors) ? ' is-visible' : '' ?>" role="alert" tabindex="-1" aria-labelledby="postFormErrorTitle">
  <strong id="postFormErrorTitle">Vui lòng kiểm tra lại các trường bị lỗi bên dưới:</strong>
  <ul id="postFormErrorList">
    <?php foreach ($errors as $field => $msg): ?>
      <li><?= e((string) $msg) ?></li>
    <?php endforeach; ?>
  </ul>
</div>

<form
  id="adminPostForm"
  action="<?= e($actionUrl) ?>"
  method="post"
  enctype="multipart/form-data"
  data-is-edit="<?= $isEdit ? '1' : '0' ?>"
  data-upload-url="<?= e(base_url('/admin/posts/upload-image')) ?>"
  data-csrf="<?= e(csrf_token()) ?>"
  novalidate
>
  <?= csrf_field() ?>

  <div class="ta-form-layout">
    <div class="ta-form-stack">
      <section class="ta-card">
        <div class="ta-card-header">
          <h2 class="ta-card-title">Nội dung bài viết</h2>
        </div>
        <div class="ta-card-body">
          <div class="ta-field">
            <label for="postTitle" class="ta-label">Tiêu đề bài viết *</label>
            <input
              type="text"
              id="postTitle"
              name="title"
              class="ta-input<?= isset($errors['title']) ? ' is-invalid' : '' ?>"
              maxlength="255"
              value="<?= e((string) ($post['title'] ?? '')) ?>"
              aria-describedby="postTitleError"
              required
            >
            <p id="postTitleError" class="ta-field-error<?= isset($errors['title']) ? ' is-visible' : '' ?>"><?= e($errors['title'] ?? '') ?></p>
          </div>

          <div class="ta-field">
            <div class="ta-label-row">
              <label for="postSlug" class="ta-label">Đường dẫn thân thiện (Slug) *</label>
              <button type="button" id="regenerateSlugBtn" class="ta-btn ta-btn--outline ta-btn--sm">Tạo lại từ tiêu đề</button>
            </div>
            <input
              type="text"
              id="postSlug"
              name="slug"
              class="ta-input<?= isset($errors['slug']) ? ' is-invalid' : '' ?>"
              maxlength="280"
              value="<?= e((string) ($post['slug'] ?? '')) ?>"
              aria-describedby="postSlugError"
              required
            >
            <p id="postSlugError" class="ta-field-error<?= isset($errors['slug']) ? ' is-visible' : '' ?>"><?= e($errors['slug'] ?? '') ?></p>
          </div>

          <div class="ta-field">
            <div class="ta-label-row">
              <label for="postSummary" class="ta-label">Tóm tắt ngắn (Summary)</label>
              <span id="summaryCounter" class="ta-char-counter">0 / 500</span>
            </div>
            <textarea
              id="postSummary"
              name="summary"
              class="ta-textarea<?= isset($errors['summary']) ? ' is-invalid' : '' ?>"
              maxlength="500"
              aria-describedby="postSummaryError"
            ><?= e((string) ($post['summary'] ?? '')) ?></textarea>
            <p id="postSummaryError" class="ta-field-error<?= isset($errors['summary']) ? ' is-visible' : '' ?>"><?= e($errors['summary'] ?? '') ?></p>
          </div>

          <div class="ta-field">
            <label id="postContentLabel" for="postContent" class="ta-label">Nội dung chi tiết (WYSIWYG Editor) *</label>
            <div id="wysiwygWrapper">
              <div class="ta-editor-toolbar" role="toolbar" aria-label="Công cụ định dạng văn bản">
                <button type="button" class="ta-editor-btn" data-cmd="bold"><strong>B</strong></button>
                <button type="button" class="ta-editor-btn" data-cmd="italic"><em>I</em></button>
                <button type="button" class="ta-editor-btn" data-cmd="underline"><u>U</u></button>
                <button type="button" class="ta-editor-btn" data-cmd="formatBlock" data-val="H2">H2</button>
                <button type="button" class="ta-editor-btn" data-cmd="formatBlock" data-val="H3">H3</button>
                <button type="button" class="ta-editor-btn" data-cmd="insertUnorderedList">Danh sách</button>
                <button type="button" class="ta-editor-btn" data-cmd="formatBlock" data-val="BLOCKQUOTE">Trích dẫn</button>
              </div>
              <div
                id="postEditorSurface"
                class="ta-editor-surface<?= isset($errors['content']) ? ' is-invalid' : '' ?>"
                contenteditable="true"
                role="textbox"
                aria-multiline="true"
                aria-labelledby="postContentLabel"
                aria-describedby="postContentError"
              ><?= $post['content'] ?? '' ?></div>
            </div>
            <textarea id="postContent" name="content" hidden><?= e((string) ($post['content'] ?? '')) ?></textarea>
            <p id="postContentError" class="ta-field-error<?= isset($errors['content']) ? ' is-visible' : '' ?>"><?= e($errors['content'] ?? '') ?></p>
          </div>
        </div>
      </section>

      <section class="ta-card">
        <div class="ta-card-header">
          <h2 class="ta-card-title">Cấu hình SEO (Meta Tags)</h2>
        </div>
        <div class="ta-card-body">
          <div class="ta-field">
            <div class="ta-label-row">
              <label for="metaTitle" class="ta-label">Meta Title</label>
              <span id="metaTitleCounter" class="ta-char-counter">0 / 60 gợi ý</span>
            </div>
            <input type="text" id="metaTitle" name="meta_title" class="ta-input" maxlength="255" value="<?= e((string) ($post['meta_title'] ?? '')) ?>">
          </div>

          <div class="ta-field">
            <div class="ta-label-row">
              <label for="metaDescription" class="ta-label">Meta Description</label>
              <span id="metaDescCounter" class="ta-char-counter">0 / 160 gợi ý</span>
            </div>
            <textarea id="metaDescription" name="meta_description" class="ta-textarea" maxlength="300"><?= e((string) ($post['meta_description'] ?? '')) ?></textarea>
          </div>

          <div class="ta-field">
            <label for="metaKeywords" class="ta-label">Meta Keywords</label>
            <input type="text" id="metaKeywords" name="meta_keywords" class="ta-input" maxlength="255" value="<?= e((string) ($post['meta_keywords'] ?? '')) ?>">
          </div>
        </div>
      </section>
    </div>

    <div class="ta-form-stack">
      <section class="ta-card">
        <div class="ta-card-header">
          <h2 class="ta-card-title">Xuất bản</h2>
        </div>
        <div class="ta-card-body">
          <div class="ta-field">
            <label for="postStatus" class="ta-label">Trạng thái bài viết</label>
            <select id="postStatus" name="status" class="ta-select">
              <option value="published"<?= ($post['status'] ?? 'published') === 'published' ? ' selected' : '' ?>>Đã xuất bản (published)</option>
              <option value="draft"<?= ($post['status'] ?? '') === 'draft' ? ' selected' : '' ?>>Bản nháp (draft)</option>
            </select>
          </div>

          <button type="submit" class="ta-btn ta-btn--primary" style="width:100%;">
            <?= $isEdit ? 'Cập nhật bài viết' : 'Lưu bài viết mới' ?>
          </button>
          <a href="<?= e(base_url('/admin/posts')) ?>" class="ta-btn ta-btn--outline" style="width:100%;">Hủy bỏ</a>
        </div>
      </section>

      <section class="ta-card">
        <div class="ta-card-header">
          <h2 class="ta-card-title">Ảnh đại diện (Thumbnail)</h2>
        </div>
        <div class="ta-card-body">
          <div id="thumbnailDropzone" class="ta-dropzone">
            <img id="thumbnailPreview" class="ta-preview-img" src="<?= e($previewSrc) ?>" alt="Xem trước ảnh đại diện"<?= $hasExistingThumb ? '' : ' hidden' ?>>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4640de" stroke-width="1.75" aria-hidden="true">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <p style="font-size:13px;color:#475569;">Kéo thả ảnh vào đây hoặc dùng nút bên dưới</p>
            <input type="file" id="postThumbnail" name="thumbnail" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" aria-describedby="postThumbnailError" hidden>
            <button type="button" id="chooseThumbnailBtn" class="ta-btn ta-btn--outline ta-btn--sm">Chọn ảnh từ máy</button>
            <span id="thumbnailFileName" style="font-size:12px;color:#667085;">Hỗ trợ JPG, PNG, WEBP (&le; 2MB). Upload lên <code>uploads/posts/</code>.</span>
          </div>
          <p id="postThumbnailError" class="ta-field-error<?= isset($errors['thumbnail']) ? ' is-visible' : '' ?>"><?= e($errors['thumbnail'] ?? '') ?></p>
        </div>
      </section>
    </div>
  </div>
</form>
