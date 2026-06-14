function populateDistricts(selectEl, governorateKey) {
  selectEl.innerHTML = '<option value="">اختر المديرية</option>';
  if (governorateKey && window.districts[governorateKey]) {
    window.districts[governorateKey].forEach(function(d) {
      var opt = document.createElement('option');
      opt.value = d;
      opt.textContent = d;
      selectEl.appendChild(opt);
    });
    selectEl.disabled = false;
  } else {
    selectEl.innerHTML = '<option value="">اختر المحافظة أولاً</option>';
    selectEl.disabled = true;
  }
}

function filterDevices() {
  var search = document.getElementById('searchInput').value.toLowerCase();
  var governorate = document.getElementById('filterGovernorate').value;
  var district = document.getElementById('filterDistrict').value;
  var category = document.getElementById('filterCategory').value;
  var offer = document.getElementById('filterOffer').value;
  var condition = document.getElementById('filterCondition').value;

  document.querySelectorAll('.device-card').forEach(function(card) {
    var match = true;
    if (search) {
      var nameEl = card.querySelector('h3');
      match = nameEl && nameEl.textContent.toLowerCase().includes(search);
    }
    if (match && governorate) match = card.dataset.governorate === governorate;
    if (match && district) match = card.dataset.district === district;
    if (match && category) match = card.dataset.category === category;
    if (match && offer) match = card.dataset.offer === offer;
    if (match && condition) match = card.dataset.condition === condition;

    card.style.display = match ? '' : 'none';
  });

  var visible = document.querySelectorAll('.device-card:not([style*="display: none"])');
  var el = document.getElementById('resultsCount');
  if (el) el.textContent = visible.length + ' نتائج';
}

document.addEventListener('DOMContentLoaded', function() {
  var searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.addEventListener('input', filterDevices);

  document.querySelectorAll('.filter-select').forEach(function(el) {
    el.addEventListener('change', filterDevices);
  });

  var govSelect = document.getElementById('filterGovernorate');
  var distSelect = document.getElementById('filterDistrict');

  if (govSelect) {
    govSelect.addEventListener('change', function() {
      populateDistricts(distSelect, this.value);
      filterDevices();
    });
  }

  var clearBtn = document.getElementById('clearFilters');
  if (clearBtn) {
    clearBtn.addEventListener('click', function() {
      var search = document.getElementById('searchInput');
      if (search) search.value = '';
      document.querySelectorAll('.filter-select').forEach(function(el) {
        if (el.id !== 'filterDistrict') el.value = '';
      });
      populateDistricts(distSelect, '');
      filterDevices();
    });
  }

  filterDevices();
});
