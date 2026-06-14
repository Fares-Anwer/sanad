function initMap() {
  var mapEl = document.getElementById('map');
  if (!mapEl || typeof L === 'undefined') return showMapFallback(mapEl);

  var lat = parseFloat(mapEl.dataset.lat) || 15.5527;
  var lng = parseFloat(mapEl.dataset.lng) || 48.5164;
  var hasCoords = mapEl.dataset.lat && mapEl.dataset.lng;

  try {
    var map = L.map(mapEl, {
      center: [lat, lng],
      zoom: hasCoords ? 14 : 6,
      zoomControl: true,
      scrollWheelZoom: false
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
    if (hasCoords) {
      L.marker([lat, lng]).addTo(map).bindPopup(mapEl.dataset.title || 'موقع الجهاز');
    }
    mapEl.addEventListener('click', function() { map.scrollWheelZoom.enable(); });
  } catch (e) { showMapFallback(mapEl); }
}

function showMapFallback(el) {
  if (!el) return;
  var lat = parseFloat(el.dataset.lat);
  var lng = parseFloat(el.dataset.lng);
  var hasCoords = lat && lng;
  el.innerHTML = '<div class="flex flex-col items-center justify-center h-full bg-gray-100 rounded-xl p-6 text-center">' +
    '<img src="assets/images/map-fallback.png" alt="خريطة" class="w-16 h-16 mb-3 opacity-50">' +
    '<p class="text-text-muted text-sm">' + (hasCoords ? 'الإحداثيات: ' + lat + '، ' + lng : 'الموقع غير متوفر') + '</p>' +
    (hasCoords ? '<a href="https://www.google.com/maps?q=' + lat + ',' + lng + '" target="_blank" class="mt-3 text-primary hover:text-primary-dark text-sm font-semibold">عرض على Google Maps</a>' : '') +
    '</div>';
}

document.addEventListener('DOMContentLoaded', initMap);

function initMapPicker() {
  var picker = document.getElementById('mapPicker');
  if (!picker) return;
  var latInput = document.getElementById('latitude');
  var lngInput = document.getElementById('longitude');
  if (typeof L === 'undefined') return showManualCoords(latInput, lngInput);

  try {
    var map = L.map(picker, { center: [15.5527, 48.5164], zoom: 6, zoomControl: true });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    var marker = null;
    map.on('click', function(e) {
      placeMarker(e.latlng.lat, e.latlng.lng);
    });

    function placeMarker(lat, lng, zoom) {
      if (marker) map.removeLayer(marker);
      marker = L.marker([lat, lng], { draggable: true }).addTo(map);
      latInput.value = lat.toFixed(6);
      lngInput.value = lng.toFixed(6);
      marker.on('dragend', function() {
        latInput.value = marker.getLatLng().lat.toFixed(6);
        lngInput.value = marker.getLatLng().lng.toFixed(6);
      });
      if (zoom) map.setView([lat, lng], zoom);
    }

    var searchTimeout;
    var searchInput = document.getElementById('locationSearch');
    var searchResults = document.getElementById('searchResults');
    if (searchInput && searchResults) {
      searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 3) { searchResults.classList.add('hidden'); searchResults.innerHTML = ''; return; }
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
  } catch (e) { showManualCoords(latInput, lngInput); }
}

function showManualCoords(latInput, lngInput) {
  var picker = document.getElementById('mapPicker');
  if (picker) picker.style.display = 'none';
  var manual = document.getElementById('manualCoords');
  if (manual) manual.classList.remove('hidden');
  var mLat = document.getElementById('manualLat');
  var mLng = document.getElementById('manualLng');
  if (mLat && mLng) {
    mLat.addEventListener('input', function() { latInput.value = this.value; });
    mLng.addEventListener('input', function() { lngInput.value = this.value; });
  }
}

document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('mapPicker')) initMapPicker();
});
