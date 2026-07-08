<style>
    .compact-detail {
        --detail-card-bg: var(--bs-card-bg);
        --detail-text: var(--bs-body-color);
        --detail-muted: var(--bs-secondary-color);
        --detail-heading: var(--bs-heading-color);
        --detail-border: var(--bs-border-color);
        --detail-soft: var(--bs-body-bg);
        --detail-table-head: rgba(var(--bs-primary-rgb), .08);
        color: var(--detail-text);
    }

    .compact-detail .detail-card {
        background: var(--detail-card-bg);
        border: 1px solid var(--detail-border);
        border-radius: .5rem;
        box-shadow: 0 .25rem 1rem rgba(15, 23, 42, .05);
    }

    .compact-detail .detail-card + .detail-card {
        margin-top: 1rem;
    }

    .compact-detail .detail-header {
        padding: 1.5rem 1.75rem;
    }

    .compact-detail .detail-title {
        color: var(--detail-heading);
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: 0;
        margin: 1rem 0 0;
    }

    .compact-detail .detail-breadcrumb {
        align-items: center;
        color: var(--detail-muted);
        display: flex;
        flex-wrap: wrap;
        font-weight: 600;
        gap: .5rem;
    }

    .compact-detail .detail-breadcrumb a {
        color: var(--bs-primary);
    }

    .compact-detail .summary-grid {
        border-top: 1px solid var(--detail-border);
        display: grid;
        grid-template-columns: minmax(260px, 1.35fr) repeat(3, minmax(180px, 1fr));
    }

    .compact-detail .summary-item {
        min-width: 0;
        padding: 1.5rem 1.75rem;
    }

    .compact-detail .summary-item + .summary-item {
        border-left: 1px solid var(--detail-border);
    }

    .compact-detail .summary-label,
    .compact-detail .info-label {
        color: var(--detail-muted);
        display: block;
        font-size: .875rem;
        font-weight: 700;
        margin-bottom: .5rem;
    }

    .compact-detail .summary-code {
        color: var(--detail-heading);
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.15;
        overflow-wrap: anywhere;
    }

    .compact-detail .summary-note {
        color: var(--detail-muted);
        margin-top: .625rem;
    }

    .compact-detail .summary-value {
        color: var(--detail-heading);
        font-size: 1rem;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .compact-detail .summary-money {
        color: var(--bs-success);
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .compact-detail .detail-section {
        padding: 1.5rem 1.75rem;
    }

    .compact-detail .detail-section-title {
        border-bottom: 1px solid var(--detail-border);
        color: var(--detail-heading);
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 1.25rem;
        padding-bottom: 1rem;
    }

    .compact-detail .info-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(150px, 1fr));
    }

    .compact-detail .info-item {
        display: grid;
        gap: .25rem;
        grid-template-columns: 1.75rem minmax(0, 1fr);
        min-width: 0;
        padding: .25rem 1.25rem .25rem 0;
    }

    .compact-detail .info-item + .info-item {
        border-left: 1px solid var(--detail-border);
        padding-left: 1.25rem;
    }

    .compact-detail .info-item:nth-child(5n + 1) {
        border-left: 0;
        padding-left: 0;
    }

    .compact-detail .info-item:nth-child(n + 6) {
        margin-top: 1.25rem;
    }

    .compact-detail .info-icon {
        color: var(--detail-muted);
        font-size: 1.25rem;
        line-height: 1.25;
        margin-top: .125rem;
    }

    .compact-detail .info-value {
        color: var(--detail-heading);
        font-weight: 700;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .compact-detail .info-value a {
        color: var(--bs-primary);
    }

    .compact-detail .compact-table {
        border: 1px solid var(--detail-border);
        border-radius: .375rem;
        overflow: hidden;
    }

    .compact-detail .compact-table .table {
        margin-bottom: 0;
    }

    .compact-detail .compact-table th {
        background: var(--detail-table-head);
        color: var(--detail-heading);
        font-weight: 800;
        text-transform: none;
        white-space: nowrap;
    }

    .compact-detail .compact-table td {
        color: var(--detail-text);
        vertical-align: middle;
    }

    .compact-detail .totals-panel {
        margin-left: auto;
        margin-top: 1.25rem;
        max-width: 430px;
    }

    .compact-detail .total-line {
        align-items: center;
        border-bottom: 1px solid var(--detail-border);
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .625rem 0;
    }

    .compact-detail .total-line strong {
        color: var(--detail-heading);
    }

    .compact-detail .total-highlight {
        background: var(--detail-soft);
        border-radius: .375rem;
        border-bottom: 0;
        margin-top: .5rem;
        padding: 1rem;
    }

    .compact-detail .total-highlight strong:last-child {
        color: var(--bs-success);
        font-size: 1.25rem;
    }

    .compact-detail .compact-tabs {
        border-bottom: 1px solid var(--detail-border);
        gap: 1.25rem;
    }

    .compact-detail .compact-tabs .nav-link {
        border: 0;
        border-bottom: 3px solid transparent;
        color: var(--detail-muted);
        font-weight: 700;
        padding: 0 0 .875rem;
    }

    .compact-detail .compact-tabs .nav-link.active {
        background: transparent;
        border-bottom-color: var(--bs-primary);
        color: var(--bs-primary);
    }

    .compact-detail .empty-row {
        color: var(--detail-muted);
        padding: 1.5rem;
        text-align: center;
    }

    .compact-detail .badge {
        white-space: normal;
    }

    @media (max-width: 1199.98px) {
        .compact-detail .summary-grid,
        .compact-detail .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .compact-detail .summary-item:nth-child(odd),
        .compact-detail .info-item:nth-child(odd) {
            border-left: 0;
            padding-left: 0;
        }

        .compact-detail .summary-item:nth-child(n + 3) {
            border-top: 1px solid var(--detail-border);
        }

        .compact-detail .info-item:nth-child(n + 3) {
            margin-top: 1.25rem;
        }
    }

    @media (max-width: 767.98px) {
        .compact-detail .detail-header,
        .compact-detail .detail-section,
        .compact-detail .summary-item {
            padding: 1.25rem;
        }

        .compact-detail .summary-grid,
        .compact-detail .info-grid {
            grid-template-columns: 1fr;
        }

        .compact-detail .summary-item + .summary-item,
        .compact-detail .info-item + .info-item {
            border-left: 0;
            border-top: 1px solid var(--detail-border);
            padding-left: 0;
            padding-top: 1rem;
        }

        .compact-detail .summary-item:nth-child(n + 2),
        .compact-detail .info-item:nth-child(n + 2) {
            margin-top: 0;
        }

        .compact-detail .detail-title,
        .compact-detail .summary-code {
            font-size: 1.5rem;
        }
    }

    @media print {
        .compact-detail .detail-actions,
        .compact-detail .compact-tabs {
            display: none !important;
        }

        .compact-detail .detail-card {
            box-shadow: none;
            break-inside: avoid;
        }
    }
</style>
