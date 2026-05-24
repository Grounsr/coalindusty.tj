/* Admin JS — TinyMCE init + small UX */
(function () {
  // Initialize TinyMCE on .tinymce textareas
  if (typeof tinymce !== 'undefined') {
    tinymce.init({
      selector: 'textarea.tinymce',
      height: 380,
      menubar: false,
      plugins: 'link lists table image code autoresize fullscreen wordcount',
      toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link image table | alignleft aligncenter alignright | code fullscreen',
      content_style: "body{font-family:Inter,sans-serif;font-size:14px;background:#0a0c10;color:#e6e6e6;}",
      skin: 'oxide-dark',
      content_css: 'dark',
      branding: false,
      promotion: false,
      block_formats: 'Параграф=p; Заголовок 2=h2; Заголовок 3=h3; Заголовок 4=h4; Цитата=blockquote',
      images_upload_url: '/admin/upload_image.php',
      automatic_uploads: true,
      relative_urls: false,
      remove_script_host: false,
      convert_urls: false,
    });
  }

  // Tabs
  document.querySelectorAll('[data-tabs]').forEach(group => {
    const tabs = group.querySelectorAll('.tab');
    const panels = group.querySelectorAll('.tab-panel');
    tabs.forEach((t, i) => t.addEventListener('click', () => {
      tabs.forEach(x => x.classList.remove('is-active'));
      panels.forEach(x => x.classList.remove('is-active'));
      t.classList.add('is-active');
      panels[i].classList.add('is-active');
    }));
  });

  // Confirm before destructive actions
  document.querySelectorAll('a[data-confirm], button[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Вы уверены?')) e.preventDefault();
    });
  });
})();
