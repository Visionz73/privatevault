// Enhanced Notes Service Worker
// Provides offline functionality and caching

const CACHE_NAME = 'enhanced-notes-v1.0.0';
const STATIC_CACHE = 'enhanced-notes-static-v1';
const DYNAMIC_CACHE = 'enhanced-notes-dynamic-v1';

// Files to cache for offline functionality
const STATIC_FILES = [
    '/',
    '/enhanced_notes.php',
    '/css/enhanced-notes.css',
    '/js/enhanced-notes-manager.js',
    '/js/enhanced-notes-ui.js',
    '/manifest.json',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
    'https://cdn.jsdelivr.net/npm/chart.js',
    'https://d3js.org/d3.v7.min.js'
];

// API endpoints to cache dynamically
const API_ENDPOINTS = [
    '/src/api/notes.php',
    '/api/notes.php'
];

// Install event - cache static files
self.addEventListener('install', event => {
    console.log('Service Worker: Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Service Worker: Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => {
                console.log('Service Worker: Static files cached');
                self.skipWaiting();
            })
            .catch(err => {
                console.error('Service Worker: Error caching static files', err);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker: Activating...');
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Service Worker: Deleting old cache', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker: Activated');
                self.clients.claim();
            })
    );
});

// Fetch event - handle requests with caching strategy
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Handle API requests
    if (isApiRequest(request)) {
        event.respondWith(handleApiRequest(request));
        return;
    }

    // Handle static files
    if (isStaticFile(request)) {
        event.respondWith(handleStaticRequest(request));
        return;
    }

    // Handle other requests
    event.respondWith(handleDynamicRequest(request));
});

// Check if request is for API
function isApiRequest(request) {
    return API_ENDPOINTS.some(endpoint => request.url.includes(endpoint));
}

// Check if request is for static file
function isStaticFile(request) {
    return STATIC_FILES.some(file => request.url.includes(file)) ||
           request.url.includes('.css') ||
           request.url.includes('.js') ||
           request.url.includes('.png') ||
           request.url.includes('.jpg') ||
           request.url.includes('.svg') ||
           request.url.includes('.ico');
}

// Handle API requests - Network first, cache fallback
async function handleApiRequest(request) {
    try {
        // Try network first for fresh data
        const response = await fetch(request.clone());
        
        if (response.ok) {
            // Cache successful responses
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
            return response;
        }
        
        throw new Error('Network response not ok');
    } catch (error) {
        console.log('Service Worker: Network failed, trying cache', error);
        
        // Fallback to cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline response for API calls
        return new Response(
            JSON.stringify({
                error: 'Offline - Keine Internetverbindung',
                offline: true,
                cached: false
            }),
            {
                status: 503,
                statusText: 'Service Unavailable',
                headers: {
                    'Content-Type': 'application/json'
                }
            }
        );
    }
}

// Handle static files - Cache first
async function handleStaticRequest(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (error) {
        console.log('Service Worker: Failed to fetch static file', error);
        return new Response('File not available offline', { status: 404 });
    }
}

// Handle dynamic requests - Network first, cache fallback
async function handleDynamicRequest(request) {
    try {
        const response = await fetch(request);
        
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        
        return response;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (request.mode === 'navigate') {
            return caches.match('/enhanced_notes.php') || 
                   new Response('Offline - Bitte prüfen Sie Ihre Internetverbindung', {
                       status: 503,
                       headers: { 'Content-Type': 'text/html' }
                   });
        }
        
        return new Response('Resource not available offline', { status: 404 });
    }
}

// Background sync for offline note creation
self.addEventListener('sync', event => {
    console.log('Service Worker: Background sync triggered');
    
    if (event.tag === 'sync-notes') {
        event.waitUntil(syncOfflineNotes());
    }
});

// Sync offline notes when connection is restored
async function syncOfflineNotes() {
    try {
        // Get offline notes from IndexedDB
        const offlineNotes = await getOfflineNotes();
        
        for (const note of offlineNotes) {
            try {
                const response = await fetch('/src/api/notes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(note)
                });
                
                if (response.ok) {
                    // Remove from offline storage
                    await removeOfflineNote(note.tempId);
                    console.log('Service Worker: Offline note synced', note.tempId);
                }
            } catch (error) {
                console.error('Service Worker: Failed to sync note', error);
            }
        }
    } catch (error) {
        console.error('Service Worker: Background sync failed', error);
    }
}

// Push notifications for note reminders
self.addEventListener('push', event => {
    console.log('Service Worker: Push event received');
    
    const options = {
        body: 'Sie haben eine Erinnerung für eine Notiz',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        tag: 'note-reminder',
        renotify: true,
        requireInteraction: true,
        actions: [
            {
                action: 'open',
                title: 'Öffnen',
                icon: '/icons/action-open.png'
            },
            {
                action: 'dismiss',
                title: 'Schließen',
                icon: '/icons/action-close.png'
            }
        ]
    };
    
    if (event.data) {
        const data = event.data.json();
        options.body = data.body || options.body;
        options.data = data;
    }
    
    event.waitUntil(
        self.registration.showNotification('Enhanced Notes', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', event => {
    console.log('Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'open') {
        event.waitUntil(
            clients.openWindow('/enhanced_notes.php')
        );
    } else if (event.action === 'dismiss') {
        // Just close the notification
        return;
    } else {
        // Default action - open app
        event.waitUntil(
            clients.openWindow('/enhanced_notes.php')
        );
    }
});

// Helper functions for IndexedDB operations
async function getOfflineNotes() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('EnhancedNotesDB', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['offlineNotes'], 'readonly');
            const store = transaction.objectStore('offlineNotes');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = () => {
                resolve(getAllRequest.result);
            };
            
            getAllRequest.onerror = () => {
                reject(getAllRequest.error);
            };
        };
        
        request.onerror = () => {
            reject(request.error);
        };
    });
}

async function removeOfflineNote(tempId) {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('EnhancedNotesDB', 1);
        
        request.onsuccess = () => {
            const db = request.result;
            const transaction = db.transaction(['offlineNotes'], 'readwrite');
            const store = transaction.objectStore('offlineNotes');
            const deleteRequest = store.delete(tempId);
            
            deleteRequest.onsuccess = () => {
                resolve();
            };
            
            deleteRequest.onerror = () => {
                reject(deleteRequest.error);
            };
        };
        
        request.onerror = () => {
            reject(request.error);
        };
    });
}

// Cache size management
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'CACHE_SIZE') {
        event.waitUntil(getCacheSize().then(size => {
            event.ports[0].postMessage({ size });
        }));
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(clearCaches().then(() => {
            event.ports[0].postMessage({ cleared: true });
        }));
    }
});

async function getCacheSize() {
    const cacheNames = await caches.keys();
    let totalSize = 0;
    
    for (const cacheName of cacheNames) {
        const cache = await caches.open(cacheName);
        const requests = await cache.keys();
        
        for (const request of requests) {
            const response = await cache.match(request);
            if (response) {
                const arrayBuffer = await response.arrayBuffer();
                totalSize += arrayBuffer.byteLength;
            }
        }
    }
    
    return totalSize;
}

async function clearCaches() {
    const cacheNames = await caches.keys();
    await Promise.all(
        cacheNames.map(cacheName => {
            if (cacheName !== STATIC_CACHE) {
                return caches.delete(cacheName);
            }
        })
    );
}
