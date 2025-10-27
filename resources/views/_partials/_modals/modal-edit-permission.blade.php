<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Permission bearbeiten</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editPermissionForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="alert alert-warning d-flex align-items-start" role="alert">
            <span class="alert-icon"><i class="ti ti-alert-triangle"></i></span>
            <span class="mb-0 p">Beim Bearbeiten des Permission-Namens könnten Sie die Systemfunktionalität
              beeinträchtigen. Bitte stellen Sie sicher, dass Sie sich absolut sicher sind, bevor Sie fortfahren.</span>
          </div>
          <div class="row">
            <div class="col-12 mb-3">
              <label for="edit_permission_name" class="form-label">Permission Name</label>
              <input type="text" id="edit_permission_name" name="name" class="form-control" required>
            </div>
            <div class="col-12 mb-3">
              <label for="edit_guard_name" class="form-label">Guard Name</label>
              <input type="text" id="edit_guard_name" name="guard_name" class="form-control" value="web">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Abbrechen
          </button>
          <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Edit Permission Modal -->

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editPermissionModal');
    if (editModal) {
      editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const permissionId = button.getAttribute('data-permission');
        const permissionName = button.getAttribute('data-name');

        const form = document.getElementById('editPermissionForm');
        const nameInput = document.getElementById('edit_permission_name');

        form.action = `/admin/permissions/${permissionId}`;
        nameInput.value = permissionName;
      });
    }
  });
</script>