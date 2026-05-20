/**
 * Send a reaction to the server and update the UI.
 * @param {string} type - The reactionable type ('post' or 'comment')
 * @param {number} id - The ID of the reactionable model
 * @param {string} reaction - The reaction type (e.g., 'like', 'love', 'haha')
 */
export function addReaction(type, id, reaction) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/reactions/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            reactionable_type: type,
            reactionable_id: id,
            type: reaction
        })
    })
    .then(response => response.json())
    .then(data => {
        const countSpan = document.getElementById(`reactions-count-${id}`);
        if (countSpan) {
            // Safely calculate the total number of reactions
            let total = 0;
            if (data.counts && Array.isArray(data.counts)) {
                total = data.counts.reduce((sum, c) => sum + c.total, 0);
            }
            countSpan.innerText = total;
        }
        // Hide the reaction menu after selection
        toggleReactionMenu(id, type);
    })
    .catch(error => console.error('Error:', error));
}

/**
 * Toggle the visibility of the reaction menu for a specific item.
 * @param {number} id - The ID of the reactionable model
 * @param {string} type - The reactionable type ('post' or 'comment')
 */
export function toggleReactionMenu(id, type) {
    const menu = document.getElementById(`reaction-menu-${id}-${type}`);
    if (menu) {
        menu.classList.toggle('hidden');
    }
}