import './bootstrap';
import Alpine from 'alpinejs';
import { addReaction, toggleReactionMenu } from './reactions';

// Make functions globally available
window.addReaction = addReaction;
window.toggleReactionMenu = toggleReactionMenu;

window.Alpine = Alpine;
Alpine.start();