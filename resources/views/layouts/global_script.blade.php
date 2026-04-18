<script>
	'use strict';

	// ---------------------------------------------------------------
	// Global: Unsaved Changes Guard (add/edit form pages only)
	// ---------------------------------------------------------------
	$(function () {
		// Only activate on pages that have a form with CSRF token
		if ($('form input[name="_token"]').length === 0) return;

		let isDirty = false;

		// Mark dirty on any user input in a form
		$(document).on('input change', 'form input:not([type="hidden"]), form select, form textarea', function () {
			isDirty = true;
		});

		// select2 changes
		$(document).on('select2:select select2:unselect select2:clear', function () {
			isDirty = true;
		});

		// Clear dirty when a POST/PUT AJAX succeeds (form saved)
		$(document).ajaxSuccess(function (event, xhr, settings) {
			if (settings.type && (settings.type.toUpperCase() === 'POST' || settings.type.toUpperCase() === 'PUT')) {
				isDirty = false;
			}
		});

		// Browser back / refresh / close: native browser dialog
		window.addEventListener('beforeunload', function (e) {
			if (isDirty) {
				e.preventDefault();
				e.returnValue = '';
			}
		});

		// Cancel button interception (skip buttons with data-no-global-confirm)
		$(document).on('click', '.btn-label-danger:not([data-no-global-confirm]), a.btn-danger:not([data-no-global-confirm])', function (e) {
			if (!isDirty) return; // no changes — let it proceed normally

			const $el = $(this);
			const href = $el.attr('href');
			const onclickAttr = $el.attr('onclick');
			const isLink = $el.is('a');

			e.preventDefault();
			e.stopImmediatePropagation();

			Swal.fire({
				title: 'Batalkan Perubahan?',
				text: 'Data yang sudah diisi akan hilang!',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Ya, Batalkan!',
				cancelButtonText: 'Tidak',
				customClass: {
					confirmButton: 'btn btn-danger',
					cancelButton: 'btn btn-label-secondary'
				},
				buttonsStyling: false
			}).then(function (result) {
				if (result.isConfirmed) {
					isDirty = false;
					if (isLink && href && href !== '#' && href !== 'javascript:void(0)') {
						window.location.href = href;
					} else if (onclickAttr) {
						// eslint-disable-next-line no-eval
						eval(onclickAttr);
					} else {
						window.history.back();
					}
				}
			});
		});
	});

</script>

