      </div>
    </main>
  </div>

  <div class="drawer-overlay" id="overlay"></div>
  <div class="drawer" id="drawer">
    <div class="drawer-header"><h2 id="drawer-title">New Item</h2><button class="drawer-close" id="drawer-close"><i class="ti ti-x"></i></button></div>
    <div class="drawer-body" id="drawer-content"></div>
    <div class="drawer-footer">
      <button class="btn btn-secondary" id="btn-cancel">Cancel</button>
      <button class="btn btn-primary" id="btn-save">Save</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="/admin/js/admin.js"></script>
  <?php if (!empty($pageScripts)) echo $pageScripts; ?>
</body>
</html>
