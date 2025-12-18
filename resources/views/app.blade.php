<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'QR Event Validation') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div id="app"></div>

    <script>
        // Ensure Select2 is loaded after Vite bundles
        // This is a fallback in case Vite doesn't properly execute UMD wrapper
        window.addEventListener('DOMContentLoaded', function() {
            // Wait for jQuery and Select2 to be available
            const checkSelect2 = setInterval(function() {
                if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                    clearInterval(checkSelect2);
                    window.select2Loaded = true;
                } else if (window.jQuery && window.select2Module) {
                    // Try to manually initialize if module is available
                    try {
                        if (typeof window.select2Module === 'function') {
                            window.select2Module(window, window.jQuery);
                        } else if (window.select2Module.default && typeof window.select2Module.default ===
                            'function') {
                            window.select2Module.default(window, window.jQuery);
                        }
                    } catch (e) {
                        // Ignore errors
                    }
                }
            }, 100);

            // Stop checking after 5 seconds
            setTimeout(function() {
                clearInterval(checkSelect2);
            }, 5000);
        });
    </script>
</body>

</html>
