<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Neue Permission erstellen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-12 mb-3">
              <label for="permission_name" class="form-label">Permission Name</label>
              <input type="text" id="permission_name" name="name" class="form-control" placeholder="z.B. manage users"
                required>
            </div>
            <div class="col-12 mb-3">
              <label for="guard_name" class="form-label">Guard Name</label>
              <input type="text" id="guard_name" name="guard_name" class="form-control" placeholder="web" value="web">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Abbrechen
          </button>
          <button type="submit" class="btn btn-primary">Permission erstellen</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Add Permission Modal -->