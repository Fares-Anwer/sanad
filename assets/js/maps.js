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
