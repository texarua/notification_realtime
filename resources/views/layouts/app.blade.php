<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                        <li class="nav-item dropdown dropdown-notifications">
                            <a id="notifications" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Notification<span class="caret"></span>
                            </a>
                            <ul id="notificationsMenu" class="dropdown-menu dropdown-menu-right menu-notification" aria-labelledby="navbarDropdown">
                              <li class="dropdown-header">No notifications</li>
                            </ul>
                        </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@if(!auth()->guest())
    <script>
        let userId = <?php echo auth()->user()->id; ?>
    </script>
@endif
<script>
    let laravelToken = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>;

    var notifications = [];

    const NOTIFICATION_TYPES = {
        follow: 'App\\Notifications\\UserFollowed',
        newPost: 'App\\Notifications\\NewPost'
    };

    $(document).ready(function() {
        // check if there's a logged in user
        if(userId) {
            $.get('/notifications', function (data) {
                addNotifications(data, "#notifications");
            });
        }
    });

    function addNotifications(newNotifications, target) {
        notifications = _.concat(notifications, newNotifications);
        // show only last 5 notifications
        notifications.slice(0, 5);
        showNotifications(notifications, target);
    }

    function showNotifications(notifications, target) {
        if(notifications.length) {
            var htmlElements = notifications.map(function (notification) {
                return makeNotification(notification);
            });
            $(target + 'Menu').html(htmlElements.join(''));
            $(target).addClass('has-notifications')
        } else {
            $(target + 'Menu').html('<li class="dropdown-header">No notifications</li>');
            $(target).removeClass('has-notifications');
        }
    }

    // Tạo ra chuỗi Notifi
    function makeNotification(notification) {
        var to = routeNotification(notification);
        var notificationText = makeNotificationText(notification);
        return '<li><a href="' + to + '">' + notificationText + '</a></li>';
    }

    // get the notification route based on it'sotifi
    function routeNotification(notification) {
        var to = '?read=' + notification.id;
        if(notification.type === NOTIFICATION_TYPES.follow) {
            to = 'users' + to;
        }
        return '/' + to;
    }

    // get the notification text based on it's type
    function makeNotificationText(notification) {
        var text = '';

        if(notification.type === NOTIFICATION_TYPES.follow) {
        const name = notification.data.follower_name;
        text += `<strong>${name}</strong> followed you`;
        } else if(notification.type === NOTIFICATION_TYPES.newPost) {
            const name = notification.data.following_name;
            text += `<strong>${name}</strong> published a post`;
        }

        return text;
    }
    var pusherKey = '{{ env("PUSHER_APP_KEY") }}'
</script>
</html>
