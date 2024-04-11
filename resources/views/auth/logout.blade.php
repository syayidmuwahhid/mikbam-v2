<input type="hidden" name="baseL" id="baseL" value="{{ url('') }}">

<script src="{{ asset('assets/js/global.js')}}"></script>
<script>
    hapusSesi();
    const baseL = document.getElementById('baseL').value;
    window.location.href = `${baseL}/login`;
</script>