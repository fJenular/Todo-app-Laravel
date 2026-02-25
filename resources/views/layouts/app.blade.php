<!DOCTYPE html>
<html lang="en">
<head>
    <title>Task Manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Prevent white flash & set default DARK -->
    <script>
        (function () {
            try {
                const saved = localStorage.getItem('darkMode');
                if (saved === '0') {
                    document.documentElement.classList.remove('dark');
                } else {
                    // DEFAULT = DARK
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Tailwind config -->
    <script>
        tailwind = window.tailwind || {};
        tailwind.config = {
            darkMode: 'class'
        };
    </script>

    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')
</head>

<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">

    @yield('content')

    @stack('scripts')

</body>
</html>
