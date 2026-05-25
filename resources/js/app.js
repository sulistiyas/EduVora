import './bootstrap';
import Alpine from 'alpinejs';
import Swal from 'sweetalert2';

// Import Alpine.js components
import datatable from './alpine/components/datatable';
import roleSearch from './alpine/pages/roles';
import { schoolProfilesSearch, schoolDetail } from './alpine/pages/schools';
import { usersSearch, userDetail, userCreate } from './alpine/pages/users';
import academicYearSearch from './alpine/pages/academicYear';
import semesterSearch from './alpine/pages/semesters';
import roomsSearch from './alpine/pages/rooms';
import { gradeSearch, gradeDetail } from './alpine/pages/grades';
import subjectSearch from './alpine/pages/academic/subject';
import scheduleSearch  from './alpine/pages/academic/schedule';
import { studentSearch, studentDetail, studentCreate } from './alpine/pages/school/student';
import { teacherSearch, teacherDetail, teacherCreate } from './alpine/pages/school/teacher';
import { teacherScore, teacherScoreForm } from './alpine/pages/school/score';

import attendanceFilter from './alpine/components/reports/attendance/filter';
import attendanceTable from './alpine/components/reports/attendance/table';
import attendanceSummary from './alpine/components/reports/attendance/summary';
import attendanceExport from './alpine/components/reports/attendance/export';

// Make Alpine available globally before plugins
window.Alpine = Alpine;
window.Swal = Swal;

// Register Alpine plugins (must be before Alpine.start())

// Register Alpine components
Alpine.data('datatable', datatable);
Alpine.data('roleSearch', roleSearch);
Alpine.data('schoolProfilesSearch', schoolProfilesSearch);
Alpine.data('schoolDetail', schoolDetail);
Alpine.data('usersSearch', usersSearch);
Alpine.data('userDetail', userDetail);
Alpine.data('userCreate', userCreate);

Alpine.data('academicYearSearch', academicYearSearch);
Alpine.data('semesterSearch', semesterSearch);
Alpine.data('subjectSearch', subjectSearch);

Alpine.data('roomsSearch', roomsSearch);
Alpine.data('gradeSearch', gradeSearch);
Alpine.data('gradeDetail', gradeDetail);

Alpine.data('scheduleSearch', scheduleSearch);

Alpine.data('studentSearch', studentSearch);
Alpine.data('studentDetail', studentDetail);
Alpine.data('studentCreate', studentCreate);

Alpine.data('teacherSearch', teacherSearch);
Alpine.data('teacherDetail', teacherDetail);
Alpine.data('teacherCreate', teacherCreate);

Alpine.data('teacherScore', teacherScore);
Alpine.data('teacherScoreForm', teacherScoreForm);

Alpine.data('attendanceFilter',  attendanceFilter)
Alpine.data('attendanceTable',   attendanceTable)
Alpine.data('attendanceSummary', attendanceSummary)
Alpine.data('attendanceExport',  attendanceExport)

// Start Alpine
Alpine.start();
