@if(session('success'))
    <script>
        window.addEventListener('load', function() {
            toast.success("{{ session('success') }}");
        });
    </script>
@endif

@if(session('error'))
    <script>
        window.addEventListener('load', function() {
            toast.error("{{ session('error') }}");
        });
    </script>
@endif

@if(session('info'))
    <script>
        window.addEventListener('load', function() {
            toast.info("{{ session('info') }}");
        });
    </script>
@endif

@if(session('warning'))
    <script>
        window.addEventListener('load', function() {
            toast.warning("{{ session('warning') }}");
        });
    </script>
@endif