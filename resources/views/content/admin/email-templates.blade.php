@extends('layouts/layoutMaster')

@section('title', 'E-Mail-Vorlagen')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/quill/katex.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/select2/select2.scss'
])
@endsection

@section('page-style')
@vite([
  'resources/assets/vendor/scss/pages/app-email.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/quill/katex.js',
  'resources/assets/vendor/libs/quill/quill.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/block-ui/block-ui.js'
])
@endsection

@section('page-script')
@vite([
  'resources/assets/js/app-email.js'
])
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <h4 class="card-title">E-Mail-Vorlagen</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTemplateModal">
      <i class="ti ti-plus me-1"></i>
      Neue Vorlage
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Betreff</th>
            <th>Zuletzt bearbeitet</th>
            <th>Aktionen</th>
          </tr>
        </thead>
        <tbody>
          @foreach($templates as $template)
          <tr>
            <td>{{ $template->name }}</td>
            <td>{{ $template->subject }}</td>
            <td>{{ $template->updated_at->format('d.m.Y H:i') }}</td>
            <td>
              <div class="d-inline-block">
                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Bearbeiten">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Vorschau">
                  <i class="ti ti-eye"></i>
                </button>
                <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Löschen">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal für neue Vorlage -->
<div class="modal fade" id="newTemplateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Neue E-Mail-Vorlage</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="newTemplateForm">
          <div class="mb-3">
            <label for="templateName" class="form-label">Name</label>
            <input type="text" class="form-control" id="templateName" required>
          </div>
          <div class="mb-3">
            <label for="templateSubject" class="form-label">Betreff</label>
            <input type="text" class="form-control" id="templateSubject" required>
          </div>
          <div class="mb-3">
            <label for="templateContent" class="form-label">Inhalt</label>
            <div id="templateContent" class="editor"></div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="button" class="btn btn-primary">Speichern</button>
      </div>
    </div>
  </div>
</div>
@endsection 