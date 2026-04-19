export function addReaction(type, id, reaction) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    fetch('/reactions/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ reactionable_type: type, reactionable_id: id, type: reaction })
    })
    .then(response => response.json())
    .then(data => {
        const countSpan = document.getElementById(`reactions-count-${id}`);
        if (countSpan) {
            let total = data.counts?.reduce((sum, c) => sum + c.total, 0) ?? 0;
            countSpan.innerText = total;
        }
        toggleReactionMenu(id, type);
    })
    .catch(error => console.error('Error:', error));
}

export function toggleReactionMenu(id, type) {
    const menu = document.getElementById(`reaction-menu-${id}-${type}`);
    if (menu) menu.classList.toggle('hidden');
}