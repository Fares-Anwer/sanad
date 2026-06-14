function initMap() {
  var mapEl = document.getElementById('map');
  if (!mapEl) return;

  var lat = parseFloat(mapEl.dataset.lat) || 15.5527;
  var lng = parseFloat(mapEl.dataset.lng) || 48.5164;
  var hasCoords = mapEl.dataset.lat && mapEl.dataset.lng;

  if (!GOOGLE_MAPS_API_KEY) {
    showMapFallback(mapEl, lat, lng, hasCoords);
    return;
  }

  if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
    showMapFallback(mapEl, lat, lng, hasCoords);
    return;
  }

  try {
    var center = { lat: lat, lng: lng };
    var map = new google.maps.Map(mapEl, {
      center: hasCoords ? center : { lat: 15.5527, lng: 48.5164 },
      zoom: hasCoords ? 14 : 6,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      disableDefaultUI: true,
      zoomControl: true,
    });

    if (hasCoords) {
      new google.maps.Marker({
        position: center,
        map: map,
        title: mapEl.dataset.title || 'موقع الجهاز'
      });
    }
  } catch (e) {
    showMapFallback(mapEl, lat, lng, hasCoords);
  }
}

function showMapFallback(el, lat, lng, hasCoords) {
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
  var manualLat = document.getElementById('manualLat');
  var manualLng = document.getElementById('manualLng');

  if (!GOOGLE_MAPS_API_KEY || typeof google === 'undefined' || typeof google.maps === 'undefined') {
    picker.style.display = 'none';
    if (manualLat && manualLng) {
      manualLat.style.display = '';
      manualLng.style.display = '';
      var updateHidden = function() {
        latInput.value = manualLat.value;
        lngInput.value = manualLng.value;
      };
      manualLat.addEventListener('input', updateHidden);
      manualLng.addEventListener('input', updateHidden);
    }
    var instructions = document.createElement('p');
    instructions.className = 'text-sm text-text-muted mb-2';
    instructions.textContent = 'أدخل الإحداثيات يدوياً أو فعّل Google Maps API';
    picker.parentNode.insertBefore(instructions, picker.nextSibling);
    return;
  }

  if (manualLat && manualLng) {
    manualLat.style.display = 'none';
    manualLng.style.display = 'none';
  }
  picker.style.display = '';

  var instruction = document.createElement('p');
  instruction.className = 'text-sm text-primary font-semibold mb-1';
  instruction.textContent = 'تحديد الموقع';
  picker.parentNode.insertBefore(instruction, picker);

  var map = new google.maps.Map(picker, {
    center: { lat: 15.5527, lng: 48.5164 },
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    disableDefaultUI: true,
    zoomControl: true,
  });

  var marker = null;

  map.addListener('click', function(e) {
    if (marker) {
      marker.setMap(null);
    }
    marker = new google.maps.Marker({
      position: e.latLng,
      map: map,
      draggable: true,
    });
    latInput.value = e.latLng.lat();
    lngInput.value = e.latLng.lng();
    marker.addListener('click', function() {
      marker.setMap(null);
      marker = null;
      latInput.value = '';
      lngInput.value = '';
    });
  });
}

document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('mapPicker')) initMapPicker();
});
