document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            fetch(`search_suggestions.php?type=sitemap&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    // Handle suggestions
                    showSuggestions(data);
                });
        }
    });
});

function showSuggestions(suggestions) {
    let suggestionBox = document.getElementById('searchSuggestions');
    if (!suggestionBox) {
        suggestionBox = document.createElement('div');
        suggestionBox.id = 'searchSuggestions';
        suggestionBox.className = 'search-suggestions';
        document.querySelector('.search-container').appendChild(suggestionBox);
    }
    
    if (suggestions.length > 0) {
        const html = suggestions.map(item => `
            <div class="suggestion-item">
                <i class="fas ${getIconForType(item.type)}"></i>
                <span>${item.title}</span>
            </div>
        `).join('');
        suggestionBox.innerHTML = html;
        suggestionBox.style.display = 'block';
    } else {
        suggestionBox.style.display = 'none';
    }
}

function getIconForType(type) {
    const icons = {
        location: 'fa-map-marker-alt',
        department: 'fa-building',
        facility: 'fa-door-open'
    };
    return icons[type] || 'fa-search';
} 