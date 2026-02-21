# Button Permission Implementation Guide

## Overview
Implement Spatie Permission checks on Add, Edit, and Delete buttons across the application using the `@can()` Blade directive.

## Implementation Pattern

### 1. Controller Setup
Add permission data to your controller's view methods:

```php
use App\Helpers\PermissionHelper;

public function index()
{
    // ... existing code ...
    
    // Get permission data for the module
    $permissionData = PermissionHelper::getViewPermissions('module_name');
    
    // Merge with existing data
    return view('your.view', array_merge([
        'data' => $data,
    ], $permissionData));
}
```

### 2. Module Naming Convention
Permission keys are automatically generated from module names:
- `master_data_product` → `$canViewProduct`, `$canCreateProduct`, `$canEditProduct`, `$canDeleteProduct`, `$canApproveProduct`
- `main_transaction_reimbursement` → `$canViewReimbursement`, `$canCreateReimbursement`, etc.
- `main_transaction_reimbursement_approval` → `$canViewReimbursementApproval`, `$canCreateReimbursementApproval`, etc.

### 3. Add Button (Blade Template)
Wrap the "Tambah Data" button with `@can()` directive:

```blade
@can('module_name.create')
    <button type="button" id="btn-add-items"
        class="btn btn-md btn-primary waves-effect waves-light">
        <span class="icon-xs icon-base ti tabler-plus me-2"></span> Tambah Data
    </button>
@endcan
```

### 4. JavaScript Permission Variables
At the top of your DataTable script, define permission variables:

```javascript
<script>
    $(function() {
        // Permission variables from controller
        const canRead = @json($canViewModule ?? false);
        const canWrite = @json($canCreateModule ?? false);
        const canDelete = @json($canDeleteModule ?? false);
        const canShowEditButton = @json($canEditModule ?? false);
        const canShowDeleteButton = @json($canDeleteModule ?? false);
        
        // Your DataTable initialization...
    });
</script>
```

### 5. Edit Button (Dynamic Rendering)
Conditionally render edit buttons in DataTable action column:

```javascript
{
    data: 'id',
    responsivePriority: 1,
    render: function(data, type, row, meta) {
        const detailUrl = `{{ route('module.detail', ':id') }}`
            .replace(':id', row.id);
        const editUrl = `{{ route('module.edit', ':id') }}`
            .replace(':id', row.id);
        
        let buttons = `<div class="d-flex gap-2">`;
        
        // Detail button (uses canRead permission)
        if (canRead) {
            buttons += `<a href="${detailUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Detail">
                    <i class="icon-base ti tabler-eye"></i>
                </a>`;
        }
        
        // Edit button (uses canShowEditButton permission)
        if (canShowEditButton) {
            buttons += `<a href="${editUrl}" class="btn btn-sm btn-primary waves-effect waves-light" title="Edit">
                    <i class="icon-base ti tabler-edit"></i>
                </a>`;
        }
        
        buttons += `</div>`;
        return buttons;
    }
}
```

### 6. Delete Button (AJAX)
For delete buttons with AJAX, check permission before showing:

```javascript
// In DataTable render function
if (canShowDeleteButton) {
    buttons += `<button type="button" class="btn btn-sm btn-danger waves-effect waves-light delete-btn" 
            data-id="${row.id}" title="Hapus">
        <i class="icon-base ti tabler-trash"></i>
    </button>`;
}

// Delete button click handler
$(document).on('click', '.delete-btn', function() {
    const itemId = $(this).data('id');
    
    Swal.fire({
        title: 'Hapus data?',
        text: 'Tindakan ini tidak dapat dibatalkan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ route('module.delete') }}`,
                method: 'DELETE',
                data: { id: itemId, _token: '{{ csrf_token() }}' },
                success: function(response) {
                    // ... handle success ...
                }
            });
        }
    });
});
```

## Permission Names

### Standard Permission Pattern
- `module.view` or `module.read` → View/Read access
- `module.create` or `module.write` → Create access
- `module.edit` or `module.write` → Edit access
- `module.delete` → Delete access
- `module.approve` → Approval access

### Examples
```
master_data_product.create
master_data_product.view
master_data_product.edit
master_data_product.delete

main_transaction_reimbursement.create
main_transaction_reimbursement.view
main_transaction_reimbursement_approval.approve
```

## Implementation Checklist

For each module:
- [ ] Add `PermissionHelper::getViewPermissions()` call in controller
- [ ] Wrap "Tambah Data" button with `@can('module.create')`
- [ ] Add permission variables to JavaScript section
- [ ] Update DataTable button rendering to check permissions
- [ ] Update delete AJAX handlers to check permissions
- [ ] Test with different user roles to verify permissions work correctly

## Example Implementation

See `/resources/views/pages/master_data/product/index.blade.php` for a complete example.

### Key Points:
1. Controller passes permission data using `PermissionHelper::getViewPermissions()`
2. Variable names follow PascalCase derivation from module name
3. Blade `@can()` directive checks permission at button level
4. JavaScript variables allow dynamic button rendering in DataTables
5. Buttons are only shown if user has the required permission

## Troubleshooting

**Buttons not showing?**
- Verify permission names match the format: `module.action`
- Check user roles have those permissions assigned
- Confirm `PermissionHelper::getViewPermissions()` is called in controller
- Look at browser console for any JavaScript errors

**Wrong variable names?**
- Permission key is auto-derived from module name
- Remove prefixes: `main_transaction_`, `master_data_`, `operasional_`
- Convert remaining to PascalCase: `reimbursement_approval` → `ReimbursementApproval`
- Final variable: `$canCreateReimbursementApproval`

## Backend Authorization

Always validate permissions on the backend as well:

```php
// In controller method
if (!auth()->user()->hasPermissionTo('module.create')) {
    abort(403, 'Unauthorized');
}
```

This ensures users cannot bypass frontend checks by making direct API calls.
