@if (session('success'))
  <div class="alert alert-success">
    <span>{{ session('success') }}</span>
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger">
    <span>{{ session('error') }}</span>
  </div>
@endif

@if ($errors->any())
  <div class="alert alert-danger">
    <div>
      <div style="font-weight: 600; margin-bottom: 4px;">Terdapat kesalahan pada formulir:</div>
      <ul style="padding-left: 20px;">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  </div>
@endif

