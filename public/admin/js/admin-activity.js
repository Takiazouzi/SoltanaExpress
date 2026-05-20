(function() {
  'use strict';
  
  const API = '/admin/Activity.php';
  const feed = document.getElementById('activity-feed');
  const refreshBtn = document.getElementById('refresh-activity');
  
  // Format timestamp to relative time
  function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
  }
  
  // Render activity item with proper HTML structure
  function renderItem(item) {
    const statusClass = item.status ? `activity-status ${item.status}` : '';
    const statusHtml = item.status ? `<span class="${statusClass}">${item.status}</span>` : '';
    const linkHtml = item.link && item.link !== '#' 
      ? `<a href="${item.link}" class="activity-link">View →</a>` 
      : '';
    
    return `
      <div class="activity-item" data-id="${item.id}">
        <div class="activity-icon ${item.type}">${item.icon}</div>
        <div class="activity-content">
          <div class="activity-top">
            <span class="activity-title">${item.title}</span>
            <span class="activity-time">${timeAgo(item.timestamp)}</span>
          </div>
          <div class="activity-desc">${item.description}</div>
          <div class="activity-meta">
            <span class="activity-user">${item.user}</span>
            ${statusHtml}
            ${linkHtml}
          </div>
        </div>
      </div>
    `;
  }
  
  // Load and render activity
  async function loadActivity() {
    if (!feed) return;
    
    feed.innerHTML = '<div class="activity-empty">Loading...</div>';
    
    try {
      const res = await fetch(API, { credentials: 'same-origin' });
      
      // Check for valid JSON response
      const contentType = res.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        throw new Error('Invalid response type');
      }
      
      const data = await res.json();
      
      if (!data.success || !data.data || data.data.length === 0) {
        feed.innerHTML = '<div class="activity-empty">No recent activity</div>';
        return;
      }
      
      feed.innerHTML = data.data.map(renderItem).join('');
      console.log(`✅ Activity feed loaded: ${data.data.length} items`);
      
    } catch (e) {
      console.error('Failed to load activity:', e);
      feed.innerHTML = '<div class="activity-empty" style="color:#DC2626">Failed to load</div>';
    }
  }
  
  // Refresh button handler
  if (refreshBtn) {
    refreshBtn.addEventListener('click', function() {
      const originalHtml = this.innerHTML;
      this.disabled = true;
      this.innerHTML = '<i class="ti ti-loader"></i>';
      
      loadActivity().finally(() => {
        this.disabled = false;
        this.innerHTML = originalHtml;
      });
    });
  }
  
  // Auto-refresh every 60 seconds (optional)
  // setInterval(loadActivity, 60000);
  
  // Init on load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadActivity);
  } else {
    loadActivity();
  }
})();
