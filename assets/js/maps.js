function runWhenReady(fn) {
  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(fn, 1);
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
}

function initMap() {
  var mapEl = document.getElementById('map');
  if (!mapEl) return;

  var rawLat = parseFloat(mapEl.dataset.lat);
  var rawLng = parseFloat(mapEl.dataset.lng);
  var hasCoords = !isNaN(rawLat) && !isNaN(rawLng) && (rawLat !== 0 || rawLng !== 0);

  if (!hasCoords) {
    return showMapFallback(mapEl);
  }

  try {
    var embedUrl = 'https://maps.google.com/maps?q=' + rawLat + ',' + rawLng + '&hl=ar&z=14&output=embed';
    
    mapEl.innerHTML = '<div class="w-full h-full min-h-[256px] relative rounded-xl overflow-hidden shadow-inner bg-gray-100">' +
      '<iframe src="' + embedUrl + '" class="w-full h-full min-h-[256px]" style="border:0; width:100%; height:100%; min-height:256px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>' +
      '</div>';
  } catch (e) {
    console.error('Error rendering Google Maps embed:', e);
    showMapFallback(mapEl);
  }
}

function showMapFallback(el) {
  if (!el) return;
  var rawLat = parseFloat(el.dataset.lat);
  var rawLng = parseFloat(el.dataset.lng);
  var hasCoords = !isNaN(rawLat) && !isNaN(rawLng) && (rawLat !== 0 || rawLng !== 0);

  el.innerHTML = '<div class="flex flex-col items-center justify-center h-full bg-gray-100 rounded-xl p-6 text-center min-h-[200px] border border-gray-200">' +
    '<img src="assets/images/map-fallback.png" alt="خريطة" class="w-16 h-16 mb-3 opacity-50 object-contain" onerror="this.style.display=\'none\'">' +
    '<p class="text-text-muted text-sm font-medium">' + (hasCoords ? 'الإحداثيات: ' + rawLat + '، ' + rawLng : 'الموقع غير محدد على الخريطة') + '</p>' +
    (hasCoords ? '<a href="https://www.google.com/maps?q=' + rawLat + ',' + rawLng + '" target="_blank" rel="noopener noreferrer" class="mt-3 bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl text-sm font-semibold inline-flex items-center gap-2 transition shadow">' +
    '<span>📍 فتح في Google Maps</span>' +
    '</a>' : '') +
    '</div>';
}

function addTileLayerWithFallback(map) {
  var providers = [
    {
      name: 'CartoDB Voyager',
      url: 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
      options: {
        maxZoom: 19,
        subdomains: 'abcd',
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }
    },
    {
      name: 'OpenStreetMap',
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      options: {
        maxZoom: 19,
        subdomains: ['a', 'b', 'c'],
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }
    },
    {
      name: 'Esri World Street Map',
      url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
      options: {
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri'
      }
    }
  ];

  var currentProviderIndex = 0;
  var currentLayer = null;
  var tileErrorCount = 0;

  function loadProvider(index) {
    if (index >= providers.length) return;
    var p = providers[index];
    tileErrorCount = 0;

    currentLayer = L.tileLayer(p.url, p.options);

    currentLayer.on('tileerror', function() {
      tileErrorCount++;
      if (tileErrorCount === 3 && currentProviderIndex < providers.length - 1) {
        console.warn('Tile provider ' + p.name + ' failed, switching to fallback provider...');
        if (currentLayer) map.removeLayer(currentLayer);
        currentProviderIndex++;
        loadProvider(currentProviderIndex);
      }
    });

    currentLayer.addTo(map);
  }

  loadProvider(0);
}

function initMapPicker() {
  var picker = document.getElementById('mapPicker');
  if (!picker) return;
  if (picker._leaflet_id) return; // Prevent double initialization

  var latInput = document.getElementById('latitude');
  var lngInput = document.getElementById('longitude');

  if (typeof L === 'undefined') {
    return showManualCoords(latInput, lngInput);
  }

  try {
    var rawLat = parseFloat(picker.dataset.lat);
    var rawLng = parseFloat(picker.dataset.lng);
    var hasCoords = !isNaN(rawLat) && !isNaN(rawLng) && (rawLat !== 0 || rawLng !== 0);

    var initialLat = hasCoords ? rawLat : 15.369445;
    var initialLng = hasCoords ? rawLng : 44.191007;
    var initialZoom = hasCoords ? 14 : 6;

    var map = L.map(picker, { center: [initialLat, initialLng], zoom: initialZoom, zoomControl: true });

    addTileLayerWithFallback(map);

    var marker = null;
    if (hasCoords) {
      marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
      if (latInput) latInput.value = initialLat.toFixed(6);
      if (lngInput) lngInput.value = initialLng.toFixed(6);
      marker.on('dragend', function() {
        var pos = marker.getLatLng();
        if (latInput) latInput.value = pos.lat.toFixed(6);
        if (lngInput) lngInput.value = pos.lng.toFixed(6);
      });
    }

    map.on('click', function(e) {
      placeMarker(e.latlng.lat, e.latlng.lng);
    });

    function placeMarker(lat, lng, zoom) {
      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      if (latInput) latInput.value = lat.toFixed(6);
      if (lngInput) lngInput.value = lng.toFixed(6);
      marker.on('dragend', function() {
        var pos = marker.getLatLng();
        if (latInput) latInput.value = pos.lat.toFixed(6);
        if (lngInput) lngInput.value = pos.lng.toFixed(6);
      });
      if (zoom) map.setView([lat, lng], zoom);
    }

    var scheduleResizes = [100, 300, 800, 1500];
    scheduleResizes.forEach(function(delay) {
      setTimeout(function() {
        if (map && map.invalidateSize) map.invalidateSize();
      }, delay);
    });

    window.addEventListener('resize', function() {
      if (map && map.invalidateSize) map.invalidateSize();
    });

    var searchTimeout;
    var searchInput = document.getElementById('locationSearch');
    var searchResults = document.getElementById('searchResults');
    if (searchInput && searchResults) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 3) {
          searchResults.classList.add('hidden');
          searchResults.innerHTML = '';
          return;
        }
        searchTimeout = setTimeout(function() {
          fetch('includes/geocode.php?q=' + encodeURIComponent(q))
            .then(function(r) { return r.json(); })
            .then(function(data) {
              searchResults.innerHTML = '';
              if (data.error || !data.results || data.results.length === 0) {
                searchResults.innerHTML = '<div class="p-2 text-text-muted text-sm">لا توجد نتائج</div>';
              } else {
                data.results.forEach(function(r) {
                  var div = document.createElement('div');
                  div.className = 'p-2 hover:bg-gray-100 cursor-pointer text-sm rounded-lg transition';
                  div.textContent = r.display_name;
                  div.addEventListener('click', function() {
                    placeMarker(parseFloat(r.lat), parseFloat(r.lng), 14);
                    searchInput.value = r.display_name.split(',')[0];
                    searchResults.classList.add('hidden');
                  });
                  searchResults.appendChild(div);
                });
              }
              searchResults.classList.remove('hidden');
            })
            .catch(function() { searchResults.classList.add('hidden'); });
        }, 500);
      });

      document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
          searchResults.classList.add('hidden');
        }
      });
    }
  } catch (e) {
    console.error('Error initializing map picker:', e);
    showManualCoords(latInput, lngInput);
  }
}

function showManualCoords(latInput, lngInput) {
  var picker = document.getElementById('mapPicker');
  if (picker) picker.style.display = 'none';
  var manual = document.getElementById('manualCoords');
  if (manual) manual.classList.remove('hidden');
  var mLat = document.getElementById('manualLat');
  var mLng = document.getElementById('manualLng');
  if (mLat && mLng) {
    mLat.addEventListener('input', function() { if (latInput) latInput.value = this.value; });
    mLng.addEventListener('input', function() { if (lngInput) lngInput.value = this.value; });
  }
}

runWhenReady(function() {
  if (document.getElementById('map')) {
    initMap();
  }
  if (document.getElementById('mapPicker')) {
    initMapPicker();
  }
});
