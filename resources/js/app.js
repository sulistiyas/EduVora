import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

// Import Alpine.js components
import datatable from './alpine/components/datatable';
import roleSearch from './alpine/pages/roles';
import { schoolProfilesSearch, schoolDetail } from './alpine/pages/schools';


// Make Alpine available globally before plugins
window.Alpine = Alpine;
window.Swal = Swal;

// Register Alpine plugins (must be before Alpine.start())

// Register Alpine components
Alpine.data('datatable', datatable);
Alpine.data('roleSearch', roleSearch);
Alpine.data('schoolProfilesSearch', schoolProfilesSearch);
Alpine.data('schoolDetail', schoolDetail);

// Start Alpine
Alpine.start();
