<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Kalender | Private Vault</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media (max-width: 768px) {
            main { margin-top: 3.5rem; }
        }
        .calendar-day { min-height: 100px; }
        .other-month { opacity: 0.5; }
        .calendar-event {
            border-radius: 4px;
            padding: 2px 6px;
            margin-bottom: 2px;
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#eef7ff] via-[#f7fbff] to-[#f9fdf2] flex flex-col">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <main class="ml-0 mt-14 md:ml-64 md:mt-0 flex-1 p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Calendar Header with navigation and view options -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-900">Kalender</h1>
                    <div class="flex space-x-2">
                        <?php 
                        $prevDate = '';
                        $nextDate = '';
                        $currentDisplay = '';
                        
                        if ($view === 'month') {
                            $prevDate = date('Y-m-d', strtotime('-1 month', $timestamp));
                            $nextDate = date('Y-m-d', strtotime('+1 month', $timestamp));
                            $currentDisplay = date('F Y', $timestamp);
                        } elseif ($view === 'week') {
                            $prevDate = date('Y-m-d', strtotime('-1 week', $timestamp));
                            $nextDate = date('Y-m-d', strtotime('+1 week', $timestamp));
                            $startWeek = date('d.m', strtotime('-' . date('w', $timestamp) . ' days', $timestamp));
                            $endWeek = date('d.m.Y', strtotime('+' . (6 - date('w', $timestamp)) . ' days', $timestamp));
                            $currentDisplay = "KW " . date('W', $timestamp) . " ($startWeek - $endWeek)";
                        } else {
                            $prevDate = date('Y-m-d', strtotime('-1 day', $timestamp));
                            $nextDate = date('Y-m-d', strtotime('+1 day', $timestamp));
                            $currentDisplay = date('d.m.Y', $timestamp);
                        }
                        ?>
                        <a href="?view=<?= $view ?>&date=<?= $prevDate ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>" class="text-gray-600 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <span class="font-medium"><?= $currentDisplay ?></span>
                        <a href="?view=<?= $view ?>&date=<?= $nextDate ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>" class="text-gray-600 hover:text-gray-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="?view=<?= $view ?>&date=<?= date('Y-m-d') ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>" class="ml-4 text-sm text-blue-600 hover:underline">Heute</a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- View Toggle -->
                    <div class="inline-flex rounded-md shadow-sm">
                        <a href="?view=month&date=<?= $date ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>"
                           class="px-3 py-1.5 text-xs font-medium <?= $view === 'month' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' ?> border rounded-l-md">
                            Monat
                        </a>
                        <a href="?view=week&date=<?= $date ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>"
                           class="px-3 py-1.5 text-xs font-medium <?= $view === 'week' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' ?> border-t border-b">
                            Woche
                        </a>
                        <a href="?view=day&date=<?= $date ?>&filter=<?= $filterType ?>&group_id=<?= $filterGroupId ?>"
                           class="px-3 py-1.5 text-xs font-medium <?= $view === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' ?> border rounded-r-md">
                            Tag
                        </a>
                    </div>

                    <!-- Filter Dropdown -->
                    <div class="relative">
                        <button id="filterDropdown" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm">
                            <?php
                            if ($filterType === 'mine') {
                                echo 'Meine Termine';
                            } elseif ($filterType === 'group' && !empty($filterGroupId)) {
                                $groupName = 'Gruppe';
                                foreach ($userGroups as $g) {
                                    if ($g['id'] == $filterGroupId) {
                                        $groupName = $g['name'];
                                        break;
                                    }
                                }
                                echo "Gruppe: $groupName";
                            } else {
                                echo 'Alle Termine';
                            }
                            ?>
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div id="filterMenu" class="hidden absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-10">
                            <a href="?view=<?= $view ?>&date=<?= $date ?>&filter=all" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $filterType === 'all' ? 'bg-gray-100' : '' ?>">
                                Alle Termine
                            </a>
                            <a href="?view=<?= $view ?>&date=<?= $date ?>&filter=mine" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $filterType === 'mine' ? 'bg-gray-100' : '' ?>">
                                Meine Termine
                            </a>
                            <?php if (!empty($userGroups)): ?>
                                <div class="border-t border-gray-200 my-1"></div>
                                <?php foreach ($userGroups as $g): ?>
                                    <a href="?view=<?= $view ?>&date=<?= $date ?>&filter=group&group_id=<?= $g['id'] ?>" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= ($filterType === 'group' && $filterGroupId == $g['id']) ? 'bg-gray-100' : '' ?>">
                                        Gruppe: <?= htmlspecialchars($g['name']) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Create Event Button -->
                    <button id="createEventBtn" class="px-4 py-2 bg-blue-600 text-white rounded-md shadow-sm text-sm hover:bg-blue-700">
                        + Neuer Termin
                    </button>
                </div>
            </div>

            <!-- Calendar Content -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <?php if ($view === 'month'): ?>
                    <?php include __DIR__ . '/calendar_month_view.php'; ?>
                <?php elseif ($view === 'week'): ?>
                    <?php include __DIR__ . '/calendar_week_view.php'; ?>
                <?php elseif ($view === 'day'): ?>
                    <?php include __DIR__ . '/calendar_day_view.php'; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Create/Edit Event Modal -->
    <div id="eventModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 m-4 max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modalTitle" class="text-lg font-semibold">Neuer Termin</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="eventForm" class="space-y-4">
                <input type="hidden" id="eventId" name="event_id" value="">
                
                <div>
                    <label for="eventTitle" class="block text-sm font-medium text-gray-700 mb-1">Titel*</label>
                    <input type="text" id="eventTitle" name="title" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label for="eventDescription" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                    <textarea id="eventDescription" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div>
                    <label for="eventLocation" class="block text-sm font-medium text-gray-700 mb-1">Ort</label>
                    <input type="text" id="eventLocation" name="location"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="allDayEvent" name="all_day" class="mr-2">
                    <label for="allDayEvent" class="text-sm font-medium text-gray-700">Ganztägig</label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Startdatum*</label>
                        <input type="date" id="startDate" name="start_date" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div id="startTimeContainer">
                        <label for="startTime" class="block text-sm font-medium text-gray-700 mb-1">Startzeit</label>
                        <input type="time" id="startTime" name="start_time" value="08:00"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">Enddatum*</label>
                        <input type="date" id="endDate" name="end_date" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div id="endTimeContainer">
                        <label for="endTime" class="block text-sm font-medium text-gray-700 mb-1">Endzeit</label>
                        <input type="time" id="endTime" name="end_time" value="09:00"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Zuweisungstyp</label>
                    <div class="flex space-x-4 mb-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="assignment_type" value="none" checked class="mr-2">
                            <span class="text-sm text-gray-700">Keine Zuweisung</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="assignment_type" value="user" class="mr-2">
                            <span class="text-sm text-gray-700">Benutzer</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="assignment_type" value="group" class="mr-2">
                            <span class="text-sm text-gray-700">Gruppe</span>
                        </label>
                    </div>
                    
                    <div id="userAssignmentContainer" class="hidden">
                        <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">-- Benutzer auswählen --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="groupAssignmentContainer" class="hidden">
                        <select name="assigned_group_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">-- Gruppe auswählen --</option>
                            <?php foreach ($userGroups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="eventColor" class="block text-sm font-medium text-gray-700 mb-1">Farbe</label>
                    <input type="color" id="eventColor" name="color" value="#4A90E2"
                           class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label for="reminderMinutes" class="block text-sm font-medium text-gray-700 mb-1">Erinnerung</label>
                    <select id="reminderMinutes" name="reminder_minutes" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="0">Keine Erinnerung</option>
                        <option value="5">5 Minuten vorher</option>
                        <option value="15">15 Minuten vorher</option>
                        <option value="30" selected>30 Minuten vorher</option>
                        <option value="60">1 Stunde vorher</option>
                        <option value="1440">1 Tag vorher</option>
                    </select>
                </div>
                
                <div class="flex justify-between pt-4">
                    <div>
                        <button type="button" id="deleteEventBtn" class="px-4 py-2 bg-red-500 text-white rounded-md hidden">
                            Löschen
                        </button>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" id="cancelBtn" class="px-4 py-2 border border-gray-300 rounded-md">
                            Abbrechen
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                            Speichern
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Event Details Modal -->
    <div id="eventDetailsModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 m-4 max-w-lg w-full">
            <div class="flex justify-between items-start mb-4">
                <h3 id="detailsTitle" class="text-lg font-semibold"></h3>
                <button id="closeDetailsModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div id="eventDetailsContent" class="space-y-4">
                <p id="detailsDescription" class="text-gray-600"></p>
                <div id="detailsLocation" class="flex items-center text-gray-600 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span></span>
                </div>
                <div id="detailsDate" class="flex items-center text-gray-600 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span></span>
                </div>
                <div id="detailsAssignment" class="flex items-center text-gray-600 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span></span>
                </div>
            </div>
            <div class="flex justify-end mt-6 space-x-2">
                <button id="editEventBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">
                    Bearbeiten
                </button>
                <button id="closeDetailsBtnBottom" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                    Schließen
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter dropdown
            const filterDropdown = document.getElementById('filterDropdown');
            const filterMenu = document.getElementById('filterMenu');
            
            filterDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                filterMenu.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function() {
                filterMenu.classList.add('hidden');
            });
            
            // Event modal functionality
            const eventModal = document.getElementById('eventModal');
            const createEventBtn = document.getElementById('createEventBtn');
            const closeModal = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const eventForm = document.getElementById('eventForm');
            const allDayCheckbox = document.getElementById('allDayEvent');
            const startTimeContainer = document.getElementById('startTimeContainer');
            const endTimeContainer = document.getElementById('endTimeContainer');
            
            function toggleTimeInputs() {
                if (allDayCheckbox.checked) {
                    startTimeContainer.classList.add('hidden');
                    endTimeContainer.classList.add('hidden');
                } else {
                    startTimeContainer.classList.remove('hidden');
                    endTimeContainer.classList.remove('hidden');
                }
            }
            
            allDayCheckbox.addEventListener('change', toggleTimeInputs);
            
            // Toggle assignment type visibility
            const assignmentRadios = document.querySelectorAll('input[name="assignment_type"]');
            const userAssignmentContainer = document.getElementById('userAssignmentContainer');
            const groupAssignmentContainer = document.getElementById('groupAssignmentContainer');
            
            assignmentRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'user') {
                        userAssignmentContainer.classList.remove('hidden');
                        groupAssignmentContainer.classList.add('hidden');
                    } else if (this.value === 'group') {
                        userAssignmentContainer.classList.add('hidden');
                        groupAssignmentContainer.classList.remove('hidden');
                    } else {
                        userAssignmentContainer.classList.add('hidden');
                        groupAssignmentContainer.classList.add('hidden');
                    }
                });
            });
            
            // Open modal with today's date pre-filled
            createEventBtn.addEventListener('click', function() {
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                
                document.getElementById('eventId').value = '';
                document.getElementById('eventTitle').value = '';
                document.getElementById('eventDescription').value = '';
                document.getElementById('eventLocation').value = '';
                document.getElementById('startDate').value = formattedDate;
                document.getElementById('endDate').value = formattedDate;
                
                document.querySelector('input[name="assignment_type"][value="none"]').checked = true;
                userAssignmentContainer.classList.add('hidden');
                groupAssignmentContainer.classList.add('hidden');
                
                document.getElementById('deleteEventBtn').classList.add('hidden');
                document.getElementById('modalTitle').textContent = 'Neuer Termin';
                
                eventModal.classList.remove('hidden');
                eventModal.classList.add('flex');
            });
            
            // Close modal
            function closeEventModal() {
                eventModal.classList.add('hidden');
                eventModal.classList.remove('flex');
            }
            
            closeModal.addEventListener('click', closeEventModal);
            cancelBtn.addEventListener('click', closeEventModal);
            
            // Handle event form submission
            eventForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const eventId = formData.get('event_id');
                const url = eventId ? '/update_event.php' : '/create_event.php';
                
                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
                });
            });
            
            // Event details modal functionality
            const eventDetailsModal = document.getElementById('eventDetailsModal');
            const closeDetailsModal = document.getElementById('closeDetailsModal');
            const closeDetailsBtnBottom = document.getElementById('closeDetailsBtnBottom');
            const editEventBtn = document.getElementById('editEventBtn');
            
            // Close details modal
            function closeEventDetailsModal() {
                eventDetailsModal.classList.add('hidden');
                eventDetailsModal.classList.remove('flex');
            }
            
            closeDetailsModal.addEventListener('click', closeEventDetailsModal);
            closeDetailsBtnBottom.addEventListener('click', closeEventDetailsModal);
            
            // Handle event clicks
            window.showEventDetails = function(eventJson) {
                const event = JSON.parse(eventJson);
                
                document.getElementById('detailsTitle').textContent = event.title;
                
                const descEl = document.getElementById('detailsDescription');
                if (event.description) {
                    descEl.textContent = event.description;
                    descEl.classList.remove('hidden');
                } else {
                    descEl.classList.add('hidden');
                }
                
                const locEl = document.getElementById('detailsLocation');
                if (event.location) {
                    locEl.querySelector('span').textContent = event.location;
                    locEl.classList.remove('hidden');
                } else {
                    locEl.classList.add('hidden');
                }
                
                // Format date display
                const dateEl = document.getElementById('detailsDate');
                let dateText = '';
                
                if (event.all_day == 1) {
                    const startDate = new Date(event.start_datetime);
                    const endDate = new Date(event.end_datetime);
                    
                    if (startDate.toDateString() === endDate.toDateString()) {
                        dateText = `Ganztägig am ${startDate.toLocaleDateString()}`;
                    } else {
                        dateText = `Ganztägig vom ${startDate.toLocaleDateString()} bis ${endDate.toLocaleDateString()}`;
                    }
                } else {
                    const startDateTime = new Date(event.start_datetime);
                    const endDateTime = new Date(event.end_datetime);
                    
                    if (startDateTime.toDateString() === endDateTime.toDateString()) {
                        dateText = `${startDateTime.toLocaleDateString()} ${startDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${endDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
                    } else {
                        dateText = `${startDateTime.toLocaleDateString()} ${startDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})} - ${endDateTime.toLocaleDateString()} ${endDateTime.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}`;
                    }
                }
                
                dateEl.querySelector('span').textContent = dateText;
                
                // Assignment info
                const assignmentEl = document.getElementById('detailsAssignment');
                let assignmentText = '';
                
                if (event.group_name) {
                    assignmentText = `Gruppe: ${event.group_name}`;
                } else if (event.assignee_name) {
                    assignmentText = `Zugewiesen an: ${event.assignee_name}`;
                } else {
                    assignmentText = `Erstellt von: ${event.creator_name}`;
                }
                
                assignmentEl.querySelector('span').textContent = assignmentText;
                
                // Store event ID for edit functionality
                editEventBtn.dataset.eventId = event.id;
                
                eventDetailsModal.classList.remove('hidden');
                eventDetailsModal.classList.add('flex');
            };
            
            // Handle edit button in event details
            editEventBtn.addEventListener('click', function() {
                const eventId = this.dataset.eventId;
                
                // Fetch event details
                fetch(`/get_event.php?id=${eventId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const event = data.event;
                            
                            // Close details modal and open edit modal
                            closeEventDetailsModal();
                            
                            // Fill form with event data
                            document.getElementById('eventId').value = event.id;
                            document.getElementById('eventTitle').value = event.title;
                            document.getElementById('eventDescription').value = event.description || '';
                            document.getElementById('eventLocation').value = event.location || '';
                            document.getElementById('eventColor').value = event.color || '#4A90E2';
                            
                            // Handle dates and times
                            const startDateTime = new Date(event.start_datetime);
                            const endDateTime = new Date(event.end_datetime);
                            
                            document.getElementById('startDate').value = startDateTime.toISOString().split('T')[0];
                            document.getElementById('endDate').value = endDateTime.toISOString().split('T')[0];
                            
                            // Format time as HH:MM
                            const formatTime = (date) => {
                                return date.toTimeString().substring(0, 5);
                            };
                            
                            document.getElementById('startTime').value = formatTime(startDateTime);
                            document.getElementById('endTime').value = formatTime(endDateTime);
                            
                            // Set all day checkbox
                            document.getElementById('allDayEvent').checked = event.all_day == 1;
                            toggleTimeInputs();
                            
                            // Set reminder
                            document.getElementById('reminderMinutes').value = event.reminder_minutes || 30;
                            
                            // Handle assignment type
                            let assignmentType = 'none';
                            if (event.assigned_to) assignmentType = 'user';
                            if (event.assigned_group_id) assignmentType = 'group';
                            
                            document.querySelector(`input[name="assignment_type"][value="${assignmentType}"]`).checked = true;
                            
                            if (assignmentType === 'user') {
                                userAssignmentContainer.classList.remove('hidden');
                                groupAssignmentContainer.classList.add('hidden');
                                document.querySelector('select[name="assigned_to"]').value = event.assigned_to;
                            } else if (assignmentType === 'group') {
                                userAssignmentContainer.classList.add('hidden');
                                groupAssignmentContainer.classList.remove('hidden');
                                document.querySelector('select[name="assigned_group_id"]').value = event.assigned_group_id;
                            } else {
                                userAssignmentContainer.classList.add('hidden');
                                groupAssignmentContainer.classList.add('hidden');
                            }
                            
                            // Show delete button and update title
                            document.getElementById('deleteEventBtn').classList.remove('hidden');
                            document.getElementById('modalTitle').textContent = 'Termin bearbeiten';
                            
                            // Show modal
                            eventModal.classList.remove('hidden');
                            eventModal.classList.add('flex');
                        } else {
                            alert('Fehler beim Laden des Termins: ' + (data.message || 'Unbekannter Fehler'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
                    });
            });
            
            // Handle delete button
            document.getElementById('deleteEventBtn').addEventListener('click', function() {
                if (confirm('Möchten Sie diesen Termin wirklich löschen?')) {
                    const eventId = document.getElementById('eventId').value;
                    
                    fetch('/delete_event.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `event_id=${eventId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Fehler: ' + (data.message || 'Unbekannter Fehler'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.');
                    });
                }
            });
        });
    </script>
</body>
</html>
