$(function () {
  var $container = $('#toast-container');

  function showToast(title, message) {
    var $el = $(
      '<div class="toast cc-toast border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
        '<div class="d-flex align-items-start gap-3 p-3">' +
          '<span class="material-symbols-outlined cc-toast-icon">check_circle</span>' +
          '<div class="flex-grow-1">' +
            '<div class="fw-medium fs-12 toast-title"></div>' +
            '<div class="fs-12 toast-msg" style="opacity:.8"></div>' +
          '</div>' +
          '<button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Tutup"></button>' +
        '</div>' +
      '</div>'
    );
    $el.find('.toast-title').text(title);
    $el.find('.toast-msg').text(message);
    $container.append($el);

    var toast = new bootstrap.Toast($el[0], { delay: 3000 });
    $el.on('hidden.bs.toast', function () { $el.remove(); });
    toast.show();
  }

  // Sementara hanya simulasi; AJAX ke keranjang dibuat di Tahap 6
  $(document).on('click', '.add-to-cart-btn', function (e) {
    e.preventDefault();
    var name = $(this).data('product') || 'Produk pilihan';
    showToast('Berhasil ditambahkan!', name + ' masuk ke keranjang.');
  });
});