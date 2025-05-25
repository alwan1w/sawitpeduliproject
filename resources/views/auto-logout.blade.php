<form id="logout-form" method="POST" action="{{ filament()->getPath() }}/logout">
    @csrf
</form>

<script>
    document.getElementById('logout-form').submit();
</script>
