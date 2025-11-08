<!doctype html>

<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="../../assets/" data-template="vertical-menu-template-starter" data-bs-theme="light">
@include('layouts.header')

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.menu')
            <div class="menu-mobile-toggler d-xl-none rounded-1">
                <a href="javascript:void(0);"
                    class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
                    <i class="ti tabler-menu icon-base"></i>
                    <i class="ti tabler-chevron-right icon-base"></i>
                </a>
            </div>
            <div class="layout-page">
                @include('layouts.navbar')
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>
                    @include('layouts.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>
</body>
@include('layouts.script')
@include('layouts.global_script')
@yield('ext_js_script')
@yield('js_script')
<script>
    $.ajaxSetup({
        statusCode: {
            401: function() {
                window.location.href = '/';
            },
            419: function() {
                window.location.href = '/';
            }
        }
    });
</script>
<script>
    $.fn.dataTable.ext.errMode = 'none';

    $(document).on('error.dt', function(e, settings, techNote, message) {
        if (message.includes('401')) {
            window.location.href = '/';
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.Echo.channel("public-messages")
            .listen(".message.sent", (event) => {
                console.log("PUSH Notification Received:", event);
                const notifList = document.getElementById(
                    "notification-list");
                if (notifList) {
                    
                    let redirectUrl = '';
                    let metadata = JSON.parse(event.message.metadata);
                    if (metadata.type == "SALES_ORDER") {
                        redirectUrl = "{{ route('main_transaction.sales_order.detail', ['id' => '__ID__']) }}".replace('__ID__', metadata.id);
                    }

                    notifList.insertAdjacentHTML('afterbegin', `
                    <li class="list-group-item list-group-item-action dropdown-notifications-item" data-redirect-to="${redirectUrl}">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i
                                            class="icon-base ${event.message.icon}"></i></span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small">${event.message.title}</h6>
                                <small class="mb-1 d-block text-body">${event.message.message}</small>
                                <small class="text-body-secondary">${event.message.timestamp_text}</small>
                            </div>
                            <div class="flex-shrink-0 dropdown-notifications-actions">
                                <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                        class="badge badge-dot"></span></a>
                            </div>
                        </div>
                    </li>
                    `);

                    const bell = document.getElementById('notification-bell-important');
                    if (bell) {
                        bell.style.display = 'inline-block';
                    }
                }
            });

        document.addEventListener('click', function(e) {
            const listItem = e.target.closest('.dropdown-notifications-item');
            if (listItem) {

                const badge = listItem.querySelector('.badge');
                if (badge) {
                    badge.classList.remove('badge', 'badge-dot');
                }

                const unread = document.querySelector('.dropdown-notifications-item .badge-dot');
                const bell = document.getElementById('notification-bell-important');
                if (bell) {
                    bell.style.display = unread ? '' : 'none';
                }

                const redirectUrl = listItem.getAttribute('data-redirect-to');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            }
        });
    });


    $(function() {
        function timeAgo(dateString) {
            const now = new Date();
            const created = new Date(dateString);
            const diffMs = now - created;
            const diffMins = Math.floor(diffMs / 60000);
            const hours = Math.floor(diffMins / 60);
            const minutes = diffMins % 60;

            if (hours > 0) {
                return `${hours} hour${hours > 1 ? 's' : ''}${minutes > 0 ? ` ${minutes} minute${minutes > 1 ? 's' : ''}` : ''} ago`;
            } else if (minutes > 0) {
                return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            } else {
                return 'Just now';
            }
        }
        $.ajax({
            url: "{{ route('notification.header_list.notification_list') }}",
            method: 'GET',
            data: {
                _token: '{{ csrf_token() }}',
            },
            success: function(response) {
                console.log("Initial Notification Load:", response);

                const notifList = document.getElementById("notification-list");
                if (notifList) {
                   
                    response.forEach(function(notif) {
                        let metadata = JSON.parse(notif.metadata);
                        let redirectUrl = '';
                        if (metadata.type == "SALES_ORDER") {
                            redirectUrl = "{{ route('main_transaction.sales_order.detail', ['id' => '__ID__']) }}".replace('__ID__', metadata.id);
                        }

                        notifList.insertAdjacentHTML('beforeend', `
                        <li class="list-group-item list-group-item-action dropdown-notifications-item" data-redirect-to="${redirectUrl}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-circle bg-label-success"><i
                                                class="icon-base ${notif.icon_text}"></i></span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 small">${notif.title}</h6>
                                    <small class="mb-1 d-block text-body">${notif.message}</small>
                                    <small class="text-body-secondary">${notif.created_at} (${timeAgo(notif.created_at)})</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                    <a href="javascript:void(0)" class="dropdown-notifications-read">
                                        ${notif.is_read == 0 ? '<span class="badge badge-dot"></span>' : ''}
                                </div>
                            </div>
                        </li>
                        `);
                    });
                }
            },
        });
    });
</script>

</html>
